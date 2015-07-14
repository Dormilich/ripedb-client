<?php
// Domain.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

class Domain extends Object
{
    /**
     * Create a DOMAIN RIPE object.
     * 
     * @param string $value The reverse delegetion address/range.
     * @return self
     */
    public function __construct($value)
    {
        $this->setType('domain');
        $this->setKey('domain');
        $this->init();
        $this->setAttribute('domain', $value);
    }

    /**
     * Defines attributes for the DOMAIN RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('domain',   Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('descr',    Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('org',      Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('admin-c',  Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('tech-c',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('zone-c',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('nserver',  Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('ds-rdata', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('remarks',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('notify',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',   Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
