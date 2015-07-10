<?php

namespace Dormilich\WebService\RIPE\Adapter;

use GuzzleHttp\Client;

class Guzzle6Adapter implements ClientAdapter
{
    protected $client;

    protected $baseUri;

    public function __construct(array $options)
    {
        $options = $this->addDefaults($options);
        $this->client = new Client($options);
    }

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

    public function setBaseUri($uri)
    {
        $this->baseUri = $uri;
    }

    public function request($method, $path, $body = NULL, array $options = array())
    {
        $options['base_uri'] = $this->baseUri;

        if (is_string($body)) {
            $options['body'] = $body;
        }
        elseif (is_array($body) or ($body instanceof \JsonSerializable)) {
            $option['json'] = $body;
        }

        return $this->client->request($type, $path, $options)->getBody();
    }
}
