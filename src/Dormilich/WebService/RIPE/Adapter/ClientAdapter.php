<?php

namespace Dormilich\WebService\RIPE\Adapter;

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
     * 
     * @param string $method HTTP method.
     * @param string $path Request path.
     * @param string $body Request body.
     * @param array $options Additional request options.
     * @return array JSON parsed response body.
     */
    public function request($method, $path, $body = NULL, array $options = array());
}