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
    public function setBaseUri($uri);

    /**
     * Send a request to the targeted API URI and return the JSON parsed response body.
     * If a request error occurrs, the implementation SHOULD throw an appropriate 
     * exception to prevent further response processing. If exceptions are not used, 
     * the return value should then be an empty array.
     * 
     * @param string $method HTTP method.
     * @param string $path Request path.
     * @param string $body Request body.
     * @return array JSON parsed response body.
     */
    public function request($method, $path, $body = NULL);
}