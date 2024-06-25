<?php

namespace Test;

use Dormilich\WebService\Adapter\ClientAdapter;

/**
 * MockClient
 *
 * This class is a mock object for the connection client allowing us to inspect
 * the parameters passed to the object. It is instantiated with the result of
 * the request() method.
 */
class MockClient implements ClientAdapter
{
    /**
     * @var
     */
    public $method;
    /**
     * @var
     */
    public $url;
    /**
     * @var
     */
    public $body;

    /**
     * @var
     */
    protected $base;
    /**
     * @var
     */
    protected $response;

    /**
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @param $uri
     * @return void
     */
    public function setBaseUri($uri): void
    {
        $this->base = $uri;
    }

    /**
     * @param $method
     * @param $path
     * @param array|NULL $headers
     * @param $body
     * @return string
     */
    public function request($method, $path, array $headers = NULL, $body = NULL): string
    {
        $host = parse_url($this->base, \PHP_URL_HOST);
        $scheme = parse_url($this->base, \PHP_URL_SCHEME);
        $dir = substr($this->base, 0, strrpos($this->base, '/', strlen($host)) + 1);

        if (strpos($path, '//') === 0) {
            $this->url = $scheme . '://' . $path;
        } elseif (strpos($path, '/') === 0) {
            $this->url = $scheme . '://' . $host . $path;
        } else {
            $this->url = $dir . $path;
        }

        $this->method = $method;
        $this->body = $body;

        return $this->response;
    }
}
