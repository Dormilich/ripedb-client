<?php
// PeeringSet.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

class PeeringSet extends Object
{
    /**
     * Create a PEERING-SET RIPE object.
     * 
     * @param string $value The name of the set.
     * @return self
     */
    public function __construct($value)
    {
        $this->setType('peering-set');
        $this->setKey('peering-set');
        $this->init();
        $this->setAttribute('peering-set', $value);
    }

    /**
     * Defines attributes for the PEERING-SET RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('peering-set', Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('descr',       Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('peering',     Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('mp-peering',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('remarks',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('org',         Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('tech-c',      Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('admin-c',     Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('notify',      Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',      Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('mnt-lower',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('changed',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',      Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
