<?php
// WebService.php

namespace Dormilich\WebService\RIPE;

use Dormilich\WebService\RIPE\Exceptions\InvalidValueException;

class WebService
{
    const SANDBOX         = 'sandbox';

    const PRODUCTION      = 'production';

    const SANDBOX_HOST    = 'https://rest-test.db.ripe.net/test/';

    const PRODUCTION_HOST = 'https://rest.db.ripe.net/ripe/';

    private $config       = [];

    protected $results    = [];

    protected $client;

    /**
     * Create a webservice to request WHOIS data. 
     * 
     * @param ClientAdapter $client A connection adapter.
     * @param array $config Webservice config options 
     * @return self
     */
    public function __construct(Adapter\ClientAdapter $client, array $config = array())
    {
        $this->setOptions($config);

        $base = $this->isProduction() ? self::PRODUCTION_HOST : self::SANDBOX_HOST;

        $this->client = $client;
        $this->client->setBaseUri($base);
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
            'password'    => 'emptypassword', // pw of the test db
        ];
        $this->config = $options + $defaults;
    }

    /**
     * Whether the live database is used.
     * 
     * @return type
     */
    public function isProduction()
    {
        return strtolower($this->config['environment']) === self::PRODUCTION;
    }

    /**
     * Get the password.
     * 
     * @return string
     */
    public function getPassword()
    {
        return $this->config['password'];
    }

    /**
     * Set the password.
     * 
     * @param string $value New password.
     * @return self
     */
    public function setPassword($value)
    {
        $this->config['password'] = (string) $value;

        return $this;
    }

    /**
     * Get the current environment.
     * 
     * @return string
     */
    public function getEnvironment()
    {
        return $this->config['environment'];
    }

    /**
     * Set the environment mode.
     * 
     * @param string $value Environment name.
     * @return self
     */
    public function setEnvironment($value)
    {
        if ($value === self::PRODUCTION) {
            $this->config['environment'] = self::PRODUCTION;
        } else {
            $this->config['environment'] = self::SANDBOX;
        }

        return $this;
    }

    /**
     * Get the RPSL source variable according to the current settings.
     * 
     * @param integer $case One of the PHP constants CASE_UPPER and CASE_LOWER. Defaults to lower-case.
     * @return string
     */
    protected function getSource($case = \CASE_LOWER)
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
    protected function setObjects(array $data)
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
    protected function setVersions(array $data)
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
     * @return ObjectInterface|false
     */
    public function getResult()
    {
        return reset($this->results);
    }

    /**
     * Get all the RPSL objects returned from the request.
     * 
     * @return ObjectInterface|false
     */
    public function getAllResults()
    {
        return $this->results;
    }

    /**
     * Convert the JSON response into a RPSL object.
     * 
     * @param array $item The "object" array from the the response.
     * @return ObjectInterface
     */
    protected function createObject($item)
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
    public function createJSON(ObjectInterface $object)
    {
        $object->getAttribute('source')->addValue( $this->getSource(\CASE_UPPER) );

        // otherwise the intended exception won’t make it through
        return json_encode($object->toArray());
    }

    /**
     * Method to read the error messages from a failed request. 
     * 
     * @param string $body Response body.
     * @return array List of all errors listed in the response.
     */
    public static function getErrors($body)
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
            $list[] = vsprintf($text, array_map($map, $args));
        }

        return $list;
    }

    /**
     * Transform an array into an URL query string. The query string is not 
     * comatible to PHP (i.e. not using bracket syntax).
     * 
     * @param array $value The query parameters.
     * @param string $name The retained name of the key for recursion.
     * @return string The URL-encoded query string.
     */
    public function createQueryString(array $params, $name = NULL) {
        array_walk($params, function (&$value, $key, $name) {
            if (is_array($value)) {
                $value = $this->createQueryString($value, $key);
            } elseif ($name) {
                $value = $name . '=' . urlencode($value);
            } else {
                $value = $key . '=' . urlencode($value);
            }
        }, $name);

        return implode('&', $params); 
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
    public function search($value, $params = array())
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

        $json = $this->client->request('GET', $path);
        $this->setObjects($json);

        return count($this->results);
    }

    /**
     * Get the abuse contact for an Inet[6]num or AutNum object.
     * 
     * Note: for an IPv4 range use the Inetnum object.
     * 
     * @param mixed $value An IPv4 address, range or prefix, IPv6 address or prefix, AS number
     * @return string Abuse email or FALSE.
     */
    public function abuseContact($value)
    {
        if ($value instanceof Object) {
            $key = $value->getPrimaryKey();
        }
        elseif (filter_var($value, \FILTER_VALIDATE_IP)) {
            $key = $value;
        }
        else {
            throw new InvalidValueException('Input value is not an IP or RPSL object.');
        }
        $path = '/abuse-contact/' . $key;
        $json = $this->client->request('GET', $path);

        if (isset($json['abuse-contacts'])) {
            return $json['abuse-contacts']['email'];
        }
        return false;
    }

    /**
     * Create a RIPE object according to the current definitions in the RIPE DB.
     * This object’s attributes do not have value constraints.
     * 
     * @param string|Object $name Either a RIPE object or a RIPE object type.
     * @return Object The RPSL object from the latest definitions.
     */
    public function getObjectFromTemplate($name)
    {
        if ($name instanceof Object) {
            $type = $name->getType();
        }
        else {
            $type = strtolower($name);
        }
        $path = '/metadata/templates/' . $type;
        $json = $this->client->request('GET', $path);

        if (!isset($json['templates']['template'])) {
            return NULL;
        }

        $template = $json['templates']['template'][0];

        $object   = Object::factory($type, $template['attributes']['attribute']);
        $object['source'] = $template['source']['id'];

        return $object;
    }

    /**
     * Get the available versions of a RIPE resource.
     * 
     * @param Object $object The RIPE object of interest.
     * @return array An array containing the revision number as key and the 
     *          date and operation type as value.
     */
    public function versions(Object $object)
    {
        $path = sprintf('%s/%s/versions', $object->getType(), $object->getPrimaryKey());
        $json = $this->client->request('GET', $path);
        $this->setVersions($json);

        return $this->getAllResults();
    }

    /**
     * Get a specific version of a RIPE object. This object will always be filtered as
     * changes may only occur in filtered attributes.
     * 
     * Note: some objects do not support versions (esp. role/person).
     * 
     * @param Object $object RIPE object.
     * @param integer $version The version of this object in the RIPE DB.
     * @return Object The requested object.
     */
    public function version(Object $object, $version)
    {
        $path = sprintf('%s/%s/versions/%d?unfiltered', 
            $object->getType(), $object->getPrimaryKey(), $version
        );
        $json = $this->client->request('GET', $path);
        $this->setObjects($json);

        return $this->getResult();
    }

    /**
     * Get a RIPE object from the DB by its primary key.
     * 
     * @param Object $object RIPE Object.
     * @param array $params Additional options: unfiltered, unformatted. Default: unfiltered.
     * @return Object The requested object.
     */
    public function read(Object $object, array $params = array('unfiltered'))
    {
        $path = $object->getType() . '/' . $object->getPrimaryKey();

        if (count($params)) {
            $path .= '?' . implode('&', $params);
        }

        $json = $this->client->request('GET', $path);
        $this->setObjects($json);

        return $this->getResult();
    }

    /**
     * Make a query to the RIPE DB using the password and parse the response.
     * 
     * @param string $method An HTTP verb.
     * @param string $path The path identifying the RIPE DB object.
     * @param ObjectInterface $object RPSL object.
     * @return void
     */
    protected function send($method, $path, ObjectInterface $object = NULL)
    {
        if (NULL === $object) {
            $body = NULL;
        } 
        else {
            $body = $this->createJSON($object);
        }

        $path .= '?' . http_build_query(['password' => $this->getPassword()]);

        $json = $this->client->request($method, $path, $body);

        $this->setObjects($json);
    }

    /**
     * Create a new RIPE object in the RIPE database.
     * 
     * @param Object $object RIPE object.
     * @return Object The created object.
     */
    public function create(Object $object)
    {
        $this->send('POST', $object->getType(), $object);

        return $this->getResult();
    }

    /**
     * Modify a RIPE object in the RIPE database.
     * 
     * @param Object $object RIPE object.
     * @param array $params Optional params to pass to the query.
     * @return Object Parsed response.
     */
    public function update(Object $object)
    {
        $path = $object->getType() . '/' . $object->getPrimaryKey();
        $this->send('PUT', $path, $object);

        return $this->getResult();
    }

    /**
     * Delete a RIPE object from the RIPE database.
     * 
     * Note: the API also accepts a reason string,
     * but it is omitted for simplicity.
     * 
     * @param Object $object RIPE object.
     * @return Object The deleted object.
     */
    public function delete(Object $object)
    {
        $path = $object->getType() . '/' . $object->getPrimaryKey();
        $this->send('DELETE', $path);

        return $this->getResult();
    }
}
