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
     * Make a query to the RIPE DB and parse the response.
     * 
     * @param string $type An HTTP verb.
     * @param string $path The path identifying the RIPE DB object.
     * @param ObjectInterface $object RPSL object.
     * @return JSON decoded response body
     */
    protected function send($type, $path, ObjectInterface $object = NULL)
    {
        return $this->client->request($type, $path);
    }

    /**
     * Get a RIPE object from the DB by its primary key.
     * 
     * Note: This request does not require a password.
     * 
     * @param Object $object RIPE Object.
     * @param array $params Additional options: unfiltered, unformatted. Default: unfiltered.
     * @return Object The requested object.
     */
    public function read(Object $object, array $params = array('unfiltered'))
    {
        if (count($params)) {
            $path = '/%s/%s/%s?' . implode('&', $params);
        } else {
            $path = '/%s/%s/%s';
        }
        $path = sprintf($path, $this->getSource(), $object->getType(), $object->getPrimaryKey());
        $json = $this->send('GET', $path);
        $this->setResult($json);

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
     * @return Object The requested object.
     */
    public function version(Object $object, $version)
    {
        $path = '/%s/%s/%s/versions/%d?unfiltered';
        $path = sprintf($path, $this->getSource(), $object->getType(), $object->getPrimaryKey(), $version);
        $json = $this->send('GET', $path);
        $this->setResult($json);

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
        $path = sprintf($path, $this->getSource(), $object->getType(), $object->getPrimaryKey());
        $json = $this->send('GET', $path);

        $versions = [];

        if (isset($json['versions']['version'])) {
            foreach ($json['versions']['version'] as $version) {
                $versions[$version['revision']] = $version['date'] . ' (' . $version['operation'] . ')';
            }
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
            $path = '/search?' . $this->createQueryString($params);
        }

        $json = $this->send('GET', $path);
        $this->setResult($json);

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
        $json = $this->send('GET', $path);

        if (isset($json['abuse-contacts'])) {
            return $json['abuse-contacts']['email'];
        }
        return false;
    }

    /**
     * Create a RIPE object according to the current definitions in the RIPE DB.
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
        $json = $this->send('GET', $path);

        if (!isset($json['templates']['template'])) {
            return NULL;
        }

        $attributes = $json['templates']['template'][0]['attributes']['attribute'];

        $object = Object::factory($type, $attributes);
        $object['source'] = $json['templates']['template'][0]['source']['id'];

        return $object;
    }
}
