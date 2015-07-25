<?php
// WebService.php

namespace Dormilich\WebService\RIPE;

abstract class WebService
{
    const SANDBOX           = 'sandbox';

    const PRODUCTION        = 'production';

    const SANDBOX_HOST      = 'rest-test.db.ripe.net';

    const PRODUCTION_HOST   = 'rest.db.ripe.net';

    const SANDBOX_SOURCE    = 'test';

    const PRODUCTION_SOURCE = 'ripe';

    private $config         = [];

    protected $results      = [];

    protected $client;

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
            'ssl'         => true,
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
     * Whether SSL is used for the connection.
     * 
     * @return boolean
     */
    public function isSSL()
    {
        return $this->config['ssl'];
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
     * Get the current environment.
     * 
     * @return string
     */
    public function getEnvironment()
    {
        return $this->config['environment'];
    }

    /**
     * Get the RPSL source variable according to the current settings.
     * 
     * @param integer $case One of the PHP constants CASE_UPPER and CASE_LOWER. Defaults to lower-case.
     * @return string
     */
    protected function getSource($case = \CASE_LOWER)
    {
        $source = $this->isProduction() ? self::PRODUCTION_SOURCE : self::SANDBOX_SOURCE;

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
    protected function setResult(array $data)
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
        // no object without attributes
        if (!isset($item['attributes'])) {
            return null;
        }
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
            $object->getAttribute($value['name'])->addValue($value['value']);
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
     * Make a query to the RIPE DB and parse the response.
     * 
     * @param string $type An HTTP verb.
     * @param string $path The path identifying the RIPE DB object.
     * @param ObjectInterface $object RPSL object.
     * @return void
     */
    abstract protected function send($type, $path, ObjectInterface $object = NULL);

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

        // @see https://github.com/RIPE-NCC/whois/wiki/WHOIS-REST-API-WhoisResources
        foreach ($json['errormessages']['errormessage'] as $error) {
            $text = $error['severity'] . ': ' . $error['text'];
            if (isset($error['attribute'])) {
                $text .= ' (' . $error['attribute']['name'] . ')';
            }
            $list[] = vsprintf($text, array_map(function ($item) {
                return $item['value'];
            }, (array) $error['args']));
        }

        return $list;
    }
}
