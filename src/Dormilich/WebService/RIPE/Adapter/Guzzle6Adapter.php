<?php

namespace Dormilich\WebService\RIPE\Adapter;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Guzzle6Adapter implements ClientAdapter
{
    protected $client;

    protected $baseUri;

    /**
     * Create instance.
     * 
     * @param array $options Guzzle configuration options.
     * @return self
     */
    public function __construct(array $options)
    {
        $options = $this->addDefaults($options);
        $this->client = new Client($options);
    }

    /**
     * Add default options (type headers) to Guzzle.
     * 
     * @param array $options User provided options.
     * @return array $options Options with added JSON headers.
     */
    private function addDefaults(array $options)
    {
        $headers = [
            "Accept"       => "application/json", 
            "Content-Type" => "application/json", 
        ];

        if (!isset($options['headers'])) {
            $options['headers'] = $headers;
        }
        else {
            $option['headers'] = $headers + $option['headers'];
        }

        return $options;
    }

    /**
     * Set the Guzzle base URI.
     * 
     * @param string $uri Base URI to use.
     * @return void
     */
    public function setBaseUri($uri)
    {
        $this->baseUri = $uri;
    }

    /**
     * Send a request to the targeted API URI and return the JSON parsed response body.
     * 
     * @param string $method HTTP method.
     * @param string $path Request path.
     * @param string $body Request body.
     * @param array $options Additional Guzzle options.
     * @return array JSON parsed response body.
     */
    public function request($method, $path, $body = NULL, array $options = array())
    {
        $options['base_uri'] = $this->baseUri;

        if (is_string($body)) {
            $options['body'] = $body;
        }
        elseif (is_array($body) or ($body instanceof \JsonSerializable)) {
            $option['json'] = $body;
        }

        $response = $this->client->request($type, $path, $options);

        return json_decode($response->getBody(), true);
    }
}
