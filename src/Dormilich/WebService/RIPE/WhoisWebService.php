<?php

namespace Dormilich\WebService\RIPE;

class WhoisWebService extends WebService
{
    public function __construct(array $config = array())
    {
        $this->setOptions($config);
        $base  = $this->isSSL() ? 'https://' : 'http://';
        $base .= $this->isProduction() ? parent::PRODUCTION_HOST : parent::SANDBOX_HOST;
        $this->client = new Client([
            'base_uri' => $base, 
            'headers'  => [
                "Accept" => "application/json", 
            ],
        ]);
    }

    protected function send($type, $path, Object $object = NULL)
    {
        $body = $this->client->get($path)->getBody();

        $this->setResult(json_decode($body, true));
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
        $path = sprintf($path, $this->getSource(), $object->getType(), $object->getPrimaryKey());

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
        $path = sprintf($path, $this->getSource(), $object->getType(), $object->getPrimaryKey(), $version);

        $this->send('GET', $path);

        return $this->getResult();
    }

    public function versions(Object $object)
    {
        $path = '/%s/%s/%s/versions';
        $path = sprintf($path, $this->getSource(), $object->getType(), $object->getPrimaryKey());

        $body = $this->client->get($path)->getBody();
        $json = json_decode($body, true);

        $versions = [];
        foreach ($json['versions']['version'] as $version) {
            $versions[$version['revision']] = $version['date'];
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

    public function getObjectTemplate($name)
    {
        $path = '/metadata/templates/' . strtolower($name);
        $body = $this->client->get($path)->getBody();
        $json = json_decode($body, true);

        return $json['templates']['template']['attributes']['attribute'];
    }
}