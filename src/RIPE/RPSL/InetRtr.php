<?php
// InetRtr.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class InetRtr extends Object
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.92';

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
        $this->create('inet-rtr',  Attr::REQUIRED, Attr::SINGLE);
        $this->create('descr',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('alias',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('local-as',  Attr::REQUIRED, Attr::SINGLE);
        $this->create('ifaddr',    Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('interface', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('peer',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mp-peer',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('member-of', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('remarks',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('org',       Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('admin-c',   Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('tech-c',    Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('notify',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',    Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('source',    Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
