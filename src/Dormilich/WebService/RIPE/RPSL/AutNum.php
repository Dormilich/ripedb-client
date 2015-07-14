<?php
// AutNum.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

/**
 * Be aware that the 'sponsoring-org' and 'status' attributes 
 * must not be set/updated/deleted by the user.
 */
class AutNum extends Object
{
    /**
     * Create an AUTONOMOUS NUMBER (AUT-NUM) RIPE object.
     * 
     * @param string $value The ASN.
     * @return self
     */
    public function __construct($value)
    {
        $this->setType('aut-num');
        $this->setKey('aut-num');
        $this->init();
        $this->setAttribute('aut-num', $value);
    }

    /**
     * Defines attributes for the AUT-NUM RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('aut-num',    Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('as-name',    Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('descr',      Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('member-of',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('import-via', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('import',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mp-import',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('export-via', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('export',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mp-export',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('default',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('remarks',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('org',        Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('admin-c',    Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('tech-c',     Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('notify',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-lower',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-routes', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',     Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',     Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('sponsoring-org');
        $this->generated('status');
        $this->generated('created');
        $this->generated('last-modified');
    }
}
