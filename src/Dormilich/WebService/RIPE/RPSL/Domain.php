<?php
// Domain.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Domain extends Object
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.86';

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
        $this->create('domain',   Attr::REQUIRED, Attr::SINGLE);
        $this->create('descr',    Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('org',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('admin-c',  Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('tech-c',   Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('zone-c',   Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('nserver',  Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('ds-rdata', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('remarks',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',   Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('source',   Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
