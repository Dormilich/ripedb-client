<?php
// WebService.php

namespace Dormilich\WebService\RIPE;

use Dormilich\WebService\Adapter\ClientAdapter;
use Dormilich\WebService\RIPE\Exceptions\InvalidAttributeException;
use Dormilich\WebService\RIPE\Exceptions\InvalidValueException;

class WebService
{
    const SANDBOX         = 'sandbox';

    const PRODUCTION      = 'production';

    const SANDBOX_HOST    = 'https://rest-test.db.ripe.net';

    const PRODUCTION_HOST = 'https://rest.db.ripe.net';

    /**
     * @var array{environment: string, password: string, username: string|null, location: string}
     */
    private $config       = [];

    protected $results    = [];

    protected $client;

    /**
     * Create a webservice to request WHOIS data.
     *
     * @param ClientAdapter $client A connection adapter.
     * @param array{environment: string, password: string, username: string, location: string} $config Webservice config options
     */
    public function __construct(ClientAdapter $client, array $config = array())
    {
        $this->client = $client;
        $this->setOptions($config);
        $this->setEnvironment($this->getEnvironment());
    }

    /**
     * Set the RIPE connection options.
     *
     * - (bool) ssl: use HTTPS (true) or HTTP (false)
     * - (enum) environment: RIPE-DB (production) or TEST-DB (sandbox)
     * - (string) password: The password for the used Mntner object.
     *          (not required for the WHOIS service)
     *
     * @param array $options
     * @return void
     */
    protected function setOptions(array $options)
    {
        $defaults = [
            'environment' => self::SANDBOX,
            'password'    => '',
            'username'    => NULL,
            'location'    => self::SANDBOX_HOST,
        ];
        $this->config = $options + $defaults;
    }

    /**
     * Whether the live database is used.
     *
     * @return bool
     */
    public function isProduction(): bool
    {
        return strtolower($this->config['environment']) === self::PRODUCTION;
    }

    /**
     * Get the defined username.
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->config['username'];
    }

    /**
     * Set the username. Use NULL to pass the password in the URL (deprecated).
     *
     * @param string|null $name
     * @return void
     */
    public function setUsername($name)
    {
        if (strlen($name)) {
            $this->config['username'] = $name;
        }
        else {
            $this->config['username'] = NULL;
        }
    }

    /**
     * Get the password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->config['password'];
    }

    /**
     * Set the password.
     *
     * @param string $password New password.
     * @return self
     */
    public function setPassword(string $password): WebService
    {
        $this->config['password'] = $password;

        return $this;
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->config['location'];
    }

    /**
     * Set the base URL. If a username and password is contained, extract it and
     * save it as credentials (allowing to pass username/password/URL at once).
     *
     * @param string $url
     * @return self
     */
    public function setHost(string $url): WebService
    {
        $this->config['location'] = $this->getUrl($url);

        $this->client->setBaseUri($this->getHost());

        if ($username = parse_url($url, PHP_URL_USER)) {
            $this->setUsername($username);
        }
        if ($password = parse_url($url, PHP_URL_PASS)) {
            $this->setPassword($password);
        }

        $this->setEnvironmentFromUrl($url);

        return $this;
    }

    /**
     * Extract the base URL for the HTTP client.
     *
     * @param string $url
     * @return string
     */
    private function getUrl(string $url): string
    {
        $scheme = parse_url($url, PHP_URL_SCHEME) ?: 'https';
        $result = $scheme . '://' . parse_url($url, PHP_URL_HOST);

        if ($port = parse_url($url, PHP_URL_PORT)) {
            $result .= ':' . $port;
        }

        return $result;
    }

    /**
     * Get the current environment.
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->config['environment'];
    }

    /**
     * Set the environment mode.
     *
     * @param string $environment Environment name.
     * @return self
     */
    public function setEnvironment(string $environment): WebService
    {
        $this->config['environment'] = $environment;

        if ($environment === self::PRODUCTION) {
            $this->setHost(self::PRODUCTION_HOST);
        }
        elseif ($environment === self::SANDBOX) {
            $this->setHost(self::SANDBOX_HOST);
            $this->setUsername('TEST-DBM-MNT');
            $this->setPassword('emptypassword');
        }
        else {
            $this->setHost($this->config['location']);
        }

        return $this;
    }

    /**
     * Extract the environment from the setup URL.
     *
     * @param string $url
     * @return void
     */
    private function setEnvironmentFromUrl(string $url)
    {
        $path = parse_url($url, PHP_URL_PATH);

        if ('/ripe' === $path) {
            $this->config['environment'] = WebService::PRODUCTION;
        }
        elseif ('/test' === $path) {
            $this->config['environment'] = WebService::SANDBOX;
        }
    }

    /**
     * Get the HTTP Basic header value.
     *
     * @param ObjectInterface $object
     * @return string
     */
    protected function getBasicAuth(ObjectInterface $object): string
    {
        $user = $this->getUsername() ?: $this->getMaintainer($object);
        $key = sprintf("%s:%s", $user, $this->getPassword());

        return 'Basic ' . base64_encode($key);
    }

    /**
     * Get the maintainer of an object.
     *
     * @param ObjectInterface $object
     * @return string
     */
    private function getMaintainer(ObjectInterface $object): string
    {
        try {
            $mnt = $object->getAttribute('mnt-by')->getValue();
        } catch (InvalidAttributeException $e) {
            $mnt = [];
        }

        if (is_array($mnt)) {
            return reset($mnt) ?: '';
        }

        return (string) $mnt;
    }

    /**
     * Get the RPSL source variable according to the current settings.
     *
     * @param integer $case One of the PHP constants CASE_UPPER and CASE_LOWER. Defaults to lower-case.
     * @return string
     */
    protected function getSource(int $case = \CASE_LOWER): string
    {
        $source = $this->isProduction() ? 'ripe' : 'test';

        if (\CASE_UPPER === $case) {
            return strtoupper($source);
        }
        return $source;
    }

    /**
     * Parse the received response into RPSL objects.
     *
     * @param array $data The decoded data.
     * @return boolean
     */
    protected function setObjects(array $data): bool
    {
        $this->results = [];

        if (!isset($data['objects'])) {
            return false;
        }

        foreach ($data['objects']['object'] as $object) {
            $this->results[] = $this->createObject($object);
        }

        return true;
    }

    /**
     * Simplify the versions info. The resulting array is transformed into a
     * version => date array.
     *
     * @param array $data The decoded data.
     * @return boolean
     */
    protected function setVersions(array $data): bool
    {
        $this->results = [];

        if (!isset($data['versions'])) {
            return false;
        }

        foreach ($data['versions']['version'] as $version) {
            if (isset($version['revision'])) {
                $this->results[$version['revision']] = $version['date'] . ' (' . $version['operation'] . ')';
            }
        }

        return true;
    }

    /**
     * Get the RPSL object returned from the request.
     *
     * @return ObjectInterface|NULL
     */
    public function getResult()
    {
        return reset($this->results) ?: NULL;
    }

    /**
     * Get all the RPSL objects returned from the request.
     *
     * @return ObjectInterface[]
     */
    public function getAllResults(): array
    {
        return $this->results;
    }

    /**
     * Convert the JSON response into a RPSL object.
     *
     * @param array $item The "object" array from the the response.
     * @return ObjectInterface
     */
    protected function createObject(array $item)
    {
        $type  = $item['type'];
        $class = __NAMESPACE__ . '\\RPSL\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $type)) );

        if (isset($item['primary-key'])) {
            $pk = $item['primary-key']['attribute'][0];
        } else {
            $pk = ['name' => null, 'value' => ''];
        }

        if (class_exists($class)) {
            $object = new $class($pk['value']);
        } else {
            $object = new Dummy($type, $pk['name']);
        }

        foreach ($item['attributes']['attribute'] as $value) {
            try {
                if (count($value) < 3) {
                    $attr_val = $value['value'];
                }
                elseif ($value['name'] === 'source') {
                    $attr_val = $value['value'];
                }
                else {
                    $attr_val = new AttributeValue($value['value']);
                    if (isset($value['comment'])) {
                        $attr_val->setComment($value['comment']);
                    }
                    if (isset($value['referenced-type'])) {
                        $attr_val->setType($value['referenced-type']);
                    }
                    if (isset($value['link']) and $value['link']['type'] === 'locator') {
                        $attr_val->setLink($value['link']['href']);
                    }
                }

                $object->getAttribute($value['name'])->addValue($attr_val);
            }
            catch (\Exception $e) {
                // skip over attributes that are present in the response but do
                // not conform to the current definitions
                continue;
            }
        }

        return $object;
    }

    /**
     * Convert the RPSL objects into its JSON representation.
     *
     * @param ObjectInterface $object RPSL object.
     * @return string
     */
    public function createJSON(ObjectInterface $object): string
    {
        $source = $object->getAttribute('source');

        if (!$source->isDefined()) {
            $source->addValue( $this->getSource(\CASE_UPPER) );
        }

        // otherwise the intended exception won’t make it through
        return json_encode($object->toArray());
    }

    /**
     * Method to read the error messages from a failed request.
     *
     * @param string $body Response body.
     * @return array List of all errors listed in the response.
     */
    public static function getErrors(string $body): array
    {
        $json = json_decode($body, true);
        $list = [];

        if (!isset($json['errormessages'])) {
            return $list;
        }

        $filter = function ($item) {
            return array_key_exists('value', $item);
        };
        $map    = function ($item) {
            return $item['value'];
        };

        // @see https://github.com/RIPE-NCC/whois/wiki/WHOIS-REST-API-WhoisResources
        foreach ($json['errormessages']['errormessage'] as $error) {
            $text = $error['severity'] . ': ' . $error['text'];
            if (isset($error['attribute'])) {
                $text .= ' (' . $error['attribute']['name'] . ')';
            }
            if (!isset($error['args'])) {
                $list[] = $text;
                continue;
            }
            $args   = array_filter($error['args'], $filter);

            // ripe may return not enough args (eg empty netname)
            if(substr_count($text, '%') > count($args)) {
                $list[] = $text;
                continue;
            }

            $list[] = vsprintf($text, array_map($map, $args));
        }

        return $list;
    }

    /**
     * Transform an array into a URL query string. The query string is not
     * compatible to PHP (i.e. not using bracket syntax).
     *
     * @param array $params The query parameters.
     * @param string|null $name The retained name of the key for recursion.
     * @return string The URL-encoded query string.
     */
    public function createQueryString(array $params, $name = NULL): string
    {
        array_walk($params, function (&$value, $key, $name) {
            if (is_array($value)) {
                $value = $this->createQueryString($value, $key);
            } elseif ($name) {
                $value = $name . '=' . rawurlencode($value);
            } else {
                $value = $key . '=' . rawurlencode($value);
            }
        }, $name);

        return implode('&', $params);
    }

    /**
     * Make a GET request for the given resource and return its result as
     * parsed JSON.
     *
     * @param string $path String denoting the requested REST resource.
     * @return array JSON parsed response.
     */
    protected function query(string $path): array
    {
        $body = $this->client->request('GET', $path, [
            'Accept' => 'application/json',
        ]);

        return json_decode($body, true);
    }

    /**
     * Search for a resource.
     *
     * @param string $value The search string.
     * @param array|string $params Optional search parameters: inverse-attribute, include-tag,
     *          exclude-tag, type-filter, flags. You may pass a valid query string if that
     *          can’t be expressed as array.
     * @return integer The number of results.
     */
    public function search(string $value, $params = array()): int
    {
        if (is_string($params) and strpos($params, '=')) {
            if (false === strpos($params, 'source=')) {
                $params .= '&source=' . $this->getSource();
            }
            $params .= '&query-string=' . (string) $value;
            $path    = '/search?' . $params;
        }
        elseif (is_array($params)) {
            $params = array_merge($params, [
                "source"       => $this->getSource(),
                "query-string" => (string) $value,
            ]);
            $path = '/search?' . $this->createQueryString($params);
        }
        else {
            throw new InvalidValueException('Input value is not a query string.');
        }

        $json = $this->query($path);
        $this->setObjects($json);

        return count($this->results);
    }

    /**
     * Get the abuse contact for an Inet[6]num or AutNum object.
     *
     * Note: for an IPv4 range use the Inetnum object.
     *
     * @param string|ObjectInterface $value An IPv4 address, range or prefix, IPv6 address or prefix, AS number
     * @return string Abuse email or FALSE.
     */
    public function abuseContact($value)
    {
        if ($value instanceof ObjectInterface) {
            $key = $value->getPrimaryKey();
        }
        elseif (filter_var($value, \FILTER_VALIDATE_IP)) {
            $key = $value;
        }
        else {
            throw new InvalidValueException('Input value is not an IP or RPSL object.');
        }
        $path = '/abuse-contact/' . $key;
        $json = $this->query($path);

        if (isset($json['abuse-contacts'])) {
            return $json['abuse-contacts']['email'];
        }
        return false;
    }

    /**
     * Create a RIPE object according to the current definitions in the RIPE DB.
     * This object’s attributes do not have value constraints.
     *
     * @param string|ObjectInterface $name Either a RIPE object or a RIPE object type.
     * @return AbstractObject The RPSL object from the latest definitions.
     */
    public function getObjectFromTemplate($name)
    {
        if ($name instanceof ObjectInterface) {
            $type = $name->getType();
        }
        else {
            $type = strtolower($name);
        }
        $path = '/metadata/templates/' . $type;
        $json = $this->query($path);

        if (!isset($json['templates']['template'])) {
            return NULL;
        }

        $template = $json['templates']['template'][0];

        $object   = AbstractObject::factory($type, $template['attributes']['attribute']);
        $object['source'] = $template['source']['id'];

        return $object;
    }

    /**
     * Get the available versions of a RIPE resource.
     *
     * @param AbstractObject $object The RIPE object of interest.
     * @return array An array containing the revision number as key and the
     *          date and operation type as value.
     */
    public function versions(AbstractObject $object): array
    {
        $path = sprintf('/%s/%s/%s/versions',
            $this->getSource(), $object->getType(), $object->getPrimaryKey()
        );
        $json = $this->query($path);
        $this->setVersions($json);

        return $this->getAllResults();
    }

    /**
     * Get a specific version of a RIPE object. This object will always be filtered as
     * changes may only occur in filtered attributes.
     *
     * Note: some objects do not support versions (esp. role/person).
     *
     * @param ObjectInterface $object RIPE object.
     * @param integer $version The version of this object in the RIPE DB.
     * @return ObjectInterface The requested object.
     */
    public function version(ObjectInterface $object, int $version)
    {
        $path = sprintf('/%s/%s/%s/versions/%d?unfiltered',
            $this->getSource(), $object->getType(), $object->getPrimaryKey(), $version
        );
        $json = $this->query($path);
        $this->setObjects($json);

        return $this->getResult();
    }

    /**
     * Get a RIPE object from the DB by its primary key.
     *
     * @param ObjectInterface $object RIPE AbstractObject.
     * @param array $params Additional options: unfiltered, unformatted. Default: unfiltered.
     * @return ObjectInterface The requested object.
     */
    public function read(ObjectInterface $object, array $params = array('unfiltered'))
    {
        $path = sprintf('/%s/%s/%s',
            $this->getSource(), $object->getType(), $object->getPrimaryKey()
        );
        if (count($params)) {
            $path .= '?' . implode('&', $params);
        }

        $json = $this->query($path);
        $this->setObjects($json);

        return $this->getResult();
    }

    /**
     * Send request to the RIPE DB and parse the response.
     *
     * @param string $method An HTTP verb.
     * @param string $path The path identifying the RIPE DB object.
     * @param array $headers The request headers (containing authentication).
     * @param string|null $body The request body.
     * @return void
     */
    protected function submit(string $method, string $path, array $headers, string $body = NULL)
    {
        $headers['Accept'] = 'application/json';

        $body = $this->client->request($method, $path, $headers, $body);
        $json = json_decode($body, true);

        $this->setObjects($json);
    }

    /**
     * Create a new RIPE object in the RIPE database.
     *
     * @param ObjectInterface $object RIPE object.
     * @return ObjectInterface The created object.
     */
    public function create(ObjectInterface $object)
    {
        $path = $this->getSource() . '/' . $object->getType();
        $headers['Content-Type']  = 'application/json';
        $headers['Authorization'] = $this->getBasicAuth($object);
        $body = $this->createJSON($object);

        $this->submit('POST', $path, $headers, $body);

        return $this->getResult();
    }

    /**
     * Modify a RIPE object in the RIPE database.
     *
     * @param ObjectInterface $object RIPE object.
     * @return ObjectInterface The updated object.
     */
    public function update(ObjectInterface $object)
    {
        $path = $this->getSource() . '/' . $object->getType() . '/' . $object->getPrimaryKey();
        $headers['Content-Type']  = 'application/json';
        $headers['Authorization'] = $this->getBasicAuth($object);
        $body = $this->createJSON($object);

        $this->submit('PUT', $path, $headers, $body);

        return $this->getResult();
    }

    /**
     * Delete a RIPE object from the RIPE database.
     *
     * @param ObjectInterface $object RIPE object.
     * @param string $reason An explanation why the object is deleted.
     * @return ObjectInterface The deleted object.
     */
    public function delete(ObjectInterface $object, string $reason = NULL)
    {
        $path = $this->getSource() . '/' . $object->getType() . '/' . $object->getPrimaryKey();

        if ($reason) {
            $path .= '?' . $this->createQueryString(['reason' => $reason]);
        }

        $headers['Authorization'] = $this->getBasicAuth($object);

        $this->submit('DELETE', $path, $headers);

        return $this->getResult();
    }
}
