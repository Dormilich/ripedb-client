<?php

namespace Dormilich\WebService\RIPE;

use Dormilich\WebService\RIPE\Adapter\ClientAdapter;

class WhoisWebService extends WebService
{
    /**
     * Create a webservice to request WHOIS data. These types of request may 
     * use non-encrypted connections.
     * 
     * @param ClientAdapter $client A connection adapter.
     * @param array $config Webservice config options 
     * @return self
     */
    public function __construct(ClientAdapter $client, array $config = array())
    {
        $this->setOptions($config);

        $base  = $this->isSSL() ? 'https://' : 'http://';
        $base .= $this->isProduction() ? parent::PRODUCTION_HOST : parent::SANDBOX_HOST;

        $this->client = $client;
        $this->client->setBaseUri($base);
    }

    /**
     * {@inheritDoc}
     */
    protected function send($type, $path, ObjectInterface $object = NULL)
    {
        $json = $this->client->request($type, $path);

        $this->setResult($json);
    }

    /**
     * Get a RIPE object from the DB by its primary key.
     * 
     * Note: This request does not require a password.
     * 
     * @param Object $object RIPE Object.
     * @param array $params Additional options: unfiltered, unformatted. Default: unfiltered.
     * @return boolean Success.
     */
    public function read(Object $object, array $params = array('unfiltered'))
    {
        if (count($params)) {
            $path = '/%s/%s/%s?' . implode('&', $params);
        } else {
            $path = '/%s/%s/%s';
        }
        $path = sprintf($path, $this->getSource(\CASE_UPPER), $object->getType(), $object->getPrimaryKey());

        $this->send('GET', $path);

        return $this->getResult();
    }

    /**
     * Get a specific version of a RIPE object. This object will always be filtered as
     * changes may only occur in filtered attributes.
     * 
     * Note: some objects do not support versions (esp. role/person).
     * 
     * @param Object $object RIPE object.
     * @param integer $version The version of this object in the RIPE DB.
     * @return boolean Success.
     */
    public function version(Object $object, $version)
    {
        $path = '/%s/%s/%s/versions/%d?unfiltered';
        $path = sprintf($path, $this->getSource(\CASE_UPPER), $object->getType(), $object->getPrimaryKey(), $version);

        $this->send('GET', $path);

        return $this->getResult();
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
        $path = '/%s/%s/%s/versions';
        $path = sprintf($path, $this->getSource(\CASE_UPPER), $object->getType(), $object->getPrimaryKey());

        $body = $this->client->get($path)->getBody();
        $json = json_decode($body, true);

        $versions = [];
        foreach ($json['versions']['version'] as $version) {
            $versions[$version['revision']] = $version['date'] . '(' . $version['operation'] . ')';
        }
        return $versions;
    }

    /**
     * Search for a resource.
     * 
     * Note: This request does not require a password.
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
            $params .= '&query-string=' . (string) $value;
            // add source if missing
            if (false === strpos($params, 'source=')) {
                $params .= '&source=' . $this->getSource();
            }
            $path = '/search?' . $params;
        } 
        else {
            $params = array_merge($params, [
                "source"       => $this->getSource(), 
                "query-string" => (string) $value, 
            ]);
            $path = '/search?' . http_build_query($params);
        }

        $this->send('GET', $path);

        return count($this->results);
    }

    /**
     * Get the abuse contact for an Inet[6]num or AutNum object.
     * 
     * @param Object $object An Inet[6]num or AutNum object.
     * @return string Abuse email or FALSE.
     */
    public function abuseContact(Object $object)
    {
        $path = '/abuse-contact/' . $object->getPrimaryKey();
        $body = $this->client->get($path)->getBody();
        $json = json_decode($body, true);

        if (isset($json['abuse-contacts'])) {
            return $json['abuse-contacts']['email'];
        }
        return false;
    }

    /**
     * get the geolocation info for an IP address.
     * 
     * @param string $ip A valid IP address.
     * @return array When found it contains the latitude, longitude and the 
     *          corresponding object data (as type an primary key).
     */
    public function geolocation($ip)
    {
        if (!filter_var($ip, \FILTER_VALIDATE_IP)) {
            throw new \UnexpectedValueException('Value is not an IP address.');
        }
        $path = '/geolocation?ipkey=' . $ip;
        $body = $this->client->get($path)->getBody();
        $json = json_decode($body, true);

        $data = [];

        if (isset($json['geolocation-attributes'])) {
            $geo  = explode(' ', $json['geolocation-attributes']['location'][0]['value']);
            $data['latitude']  = $geo[0];
            $data['longitude'] = $geo[1];

            $inet = explode('/', $json['geolocation-attributes']['location'][0]['link']['xlink:href']);
            $key  = array_pop($inet);
            $type = array_pop($inet);
            $data[$type] = $key;
        }

        return $data;
    }

    /**
     * Create a RIPE object according to the current definitions in the RIPE DB.
     * 
     * @param string|Object $name Either a RIPE object or a RIPE object type.
     * @return Object The RIPE object from the latest definitions.
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
        $body = $this->client->get($path)->getBody();
        $json = json_decode($body, true);

        $attributes = $json['templates']['template'][1]['attributes']['attribute'];

        $object = Object::factory($type, $attributes);
        $object['source'] = $json['templates']['template'][0]['source']['id'];

        return $object;
    }
}
