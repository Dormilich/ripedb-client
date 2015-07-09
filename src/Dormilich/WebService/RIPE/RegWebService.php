<?php

namespace Dormilich\WebService\RIPE;

class RegWebService extends WhoisWebService
{
    public function __construct(array $config = array())
    {
        $this->setOptions($config);
        $base  = 'https://'; // RIPE requires SSL for these methods
        $base .= $this->isProduction() ? parent::PRODUCTION_HOST : parent::SANDBOX_HOST;
        $this->client = new Client([
            'base_uri' => $base, 
            'headers'  => [
                "Accept"       => "application/json", 
                "Content-Type" => "application/json", 
            ],
        ]);
    }

    protected function send($type, $path, Object $object = NULL)
    {
        if (NULL !== $object) {
            $options = [
                'body'  => $this->createJSON($object),
                'query' => [
                    'password' => $this->getPassword(), 
                ],
            ];
        } else {
            $options = [];
        }

        $body = $this->client->request($type, $path, $options)->getBody();

        $this->setResult(json_decode($body, true));
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

        $this->send('POST', $path, ['body' => $this->createJSON($object)]);

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

        $this->send('PUT', $path, ['body' => $this->createJSON($object)]);

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

        $this->send('DELETE', $path, $object);

        return $this->getResult();
    }
}