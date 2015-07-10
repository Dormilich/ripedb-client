<?php

namespace Dormilich\WebService\RIPE\Adapter;

interface ClientAdapter
{
    public function setBaseUri($uri);

    public function request($method, $path, $body = NULL, array $options = array());
}