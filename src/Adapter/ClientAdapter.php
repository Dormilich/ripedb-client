<?php

namespace Dormilich\WebService\Adapter;

interface ClientAdapter
{
    /**
     * Set the base URI that decides which database to use.
     *
     * @param string $uri Base URI to use.
     * @return void
     */
    public function setBaseUri($uri): void;

    /**
     * Send a request to the targeted API URI and return the (parsed) response body.
     * If a request error occurrs, the implementation SHOULD throw an appropriate
     * exception to prevent further response processing. If exceptions are not used,
     * the return value should then be an empty array.
     *
     * @param string $method HTTP method.
     * @param string $path Request path.
     * @param array $headers Additional per-request headers.
     * @param string $body Request body.
     * @return string (XML/JSON) response body.
     */
    public function request($method, $path, array $headers = NULL, $body = NULL): string;
}
