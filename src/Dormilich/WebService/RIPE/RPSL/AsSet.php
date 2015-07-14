<?php
// AsSet.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

class AsSet extends Object
{
    /**
     * Create an AS-SET RIPE object.
     * 
     * @param string $value The name of the AS-Set.
     * @return self
     */
    public function __construct($value)
    {
        $this->setType('as-set');
        $this->setKey('as-set');
        $this->init();
        $this->setAttribute('as-set', $value);
    }

    /**
     * Defines attributes for the AS-SET RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('as-set',      Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('descr',       Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('members',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mbrs-by-ref', Attribute::OPTIONAL, Attribute::MULTIPLE);
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
