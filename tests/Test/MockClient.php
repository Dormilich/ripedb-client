<?php

namespace Test;

use Dormilich\WebService\Adapter\ClientAdapter;

/**
 * This class is a mock object for the connection client allowing us to inspect
 * the parameters passed to the object. It is instantiated with the result of
 * the request() method.
 */
class MockClient implements ClientAdapter
{
	public $method = '';
	public $url = '';
	public $body = '';
    public $header = [];

	protected $base;
	protected $response;

	public function __construct($response)
	{
		$this->response = $response;
	}

	public function setBaseUri(string $uri)
	{
		$this->base = rtrim($uri, '/');
	}

	public function request(string $method, string $path, array $headers = NULL, $body = NULL): string
	{
        $scheme = parse_url($this->base, \PHP_URL_SCHEME);
		$host   = parse_url($this->base, \PHP_URL_HOST);
        $port   = parse_url($this->base, \PHP_URL_PORT);
        $dir    = parse_url($this->base, \PHP_URL_PATH);

        $this->url = $scheme . '://' . $host;

        if ($port) {
            $this->url .= ':' . $port;
        }

        if ('/' === $path[0]) {
            $this->url .= $path;
        }
        else {
            $this->url .= $dir . '/' . $path;
        }

		$this->method  = $method;
		$this->body    = $body;
        $this->header  = $headers;

		return $this->response;
	}
}
