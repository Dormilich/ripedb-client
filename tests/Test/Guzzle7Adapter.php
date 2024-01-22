<?php

namespace Test;

use Dormilich\WebService\Adapter\ClientAdapter;
use GuzzleHttp\Client;

class Guzzle7Adapter implements ClientAdapter
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
        $this->client = new Client($options);
    }

    /**
     * Set the Guzzle base URI.
     *
     * @param string $uri Base URI to use.
     * @return void
     */
    public function setBaseUri($uri): void
    {
        $this->baseUri = $uri;
    }

    /**
     * Send a request to the targeted API URI and return the JSON parsed response body.
     *
     * @param string $method HTTP method.
     * @param string $path Request path.
     * @param string $body Request body.
     * @return array JSON parsed response body.
     */
    public function request($method, $path, array $headers = NULL, $body = NULL): string
    {
        $options = [
            'base_uri' => $this->baseUri
        ];

        if (is_string($body)) {
            $options['body'] = $body;
        }
        elseif (is_array($body) or ($body instanceof \JsonSerializable)) {
            $options['json'] = $body;
        }

        if (!empty($headers)) {
            $options['headers'] = $headers;
        }

        $response = $this->client->request($method, $path, $options);

        return $response->getBody();
    }
}
