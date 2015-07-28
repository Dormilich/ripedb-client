<?php

namespace Dormilich\WebService\RIPE;

use Dormilich\WebService\RIPE\Adapter\ClientAdapter;

class RegWebService extends WhoisWebService
{
    /**
     * Create a webservice to request WHOIS data. These types of request must use 
     * encrypted connections, so setting the 'ssl' option to false has no effect.
     * 
     * @param ClientAdapter $client A connection adapter.
     * @param array $config Webservice config options 
     * @return self
     */
    public function __construct(ClientAdapter $client, array $config = array())
    {
        $this->setOptions($config);

        $base  = 'https://'; // RIPE requires SSL for these methods
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
     * @return ObjectInterface Response RPSL object.
     */
    protected function send($type, $path, ObjectInterface $object = NULL)
    {
        if (NULL === $object) {
            $body = NULL;
        } 
        else {
            $body = $this->createJSON($object);
        }

        $path .= '?' . http_build_query(['password' => $this->getPassword()]);

        $json = $this->client->request($type, $path, $body);

        $this->setResult($json);

        return $json;
    }

    /**
     * Create a new RIPE object in the RIPE database.
     * 
     * @param Object $object RIPE object.
     * @return Object The created object.
     */
    public function create(Object $object)
    {
        $path = sprintf('/%s/%s', $this->getSource(), $object->getType());

        $this->send('POST', $path, $object);

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
        $path = sprintf('/%s/%s/%s', 
            $this->getSource(), $object->getType(), $object->getPrimaryKey()
        );

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
        $path = sprintf('/%s/%s/%s', 
            $this->getSource(), $object->getType(), $object->getPrimaryKey()
        );

        $this->send('DELETE', $path);

        return $this->getResult();
    }
}
