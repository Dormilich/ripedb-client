<?php
// InetRtr.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

class InetRtr extends Object
{
    /**
     * Create a router (INET-RTR) RIPE object.
     * 
     * @param string $value The DNS name.
     * @return self
     */
    public function __construct($value)
    {
        $this->setType('inet-rtr');
        $this->setKey('inet-rtr');
        $this->init();
        $this->setAttribute('inet-rtr', $value);
    }

    /**
     * Defines attributes for the INET-RTR RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('inet-rtr',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('descr',     Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('alias',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('local-as',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('ifaddr',    Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('interface', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('peer',      Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mp-peer',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('member-of', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('remarks',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('org',       Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('admin-c',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('tech-c',    Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('notify',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',    Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',    Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
