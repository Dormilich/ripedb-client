<?php
// FilterSet.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

class FilterSet extends Object
{
    /**
     * Create a FILTER-SET RIPE object.
     * 
     * @param string $value The name of the set (of routers).
     * @return self
     */
    public function __construct($value)
    {
        $this->setType('filter-set');
        $this->setKey('filter-set');
        $this->init();
        $this->setAttribute('filter-set', $value);
    }

    /**
     * Defines attributes for the FILTER-SET RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('filter-set',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('descr',       Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('filter',      Attribute::OPTIONAL, Attribute::SINGLE);
        $this->create('mp-filter',   Attribute::OPTIONAL, Attribute::SINGLE);
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
