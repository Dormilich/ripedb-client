<?php
// Role.php

namespace Dormilich\WebService\RIPE\DB;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;
use Dormilich\WebService\RIPE\MatchedAttribute;

class Role extends Object
{
    const PRIMARYKEY = 'nic-hdl';

    /**
     * Create a ROLE RIPE object.
     * 
     * @param string $value NIC handle. If not specified an auto-handle is used.
     * @return self
     */
    public function __construct($value = 'AUTO-1')
    {
        $this->type = 'role';
        $this->init();
        $this->setAttribute(self::PRIMARYKEY, $value);
    }

    /**
     * Defines attributes for the ROLE RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('role',     Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('address',  Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('phone',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('fax-no',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('e-mail',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('org',      Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('admin-c',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('tech-c',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        // auto: AUTO-1, AUTO-1{[A-Z]+}, manual: {[A-Z]+}, {[A-Z]+}-RIPE, {[A-Z]+}-{2-letter country code}
        $this->create('nic-hdl',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('remarks',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('notify',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',   Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('abuse-mailbox', Attribute::OPTIONAL, Attribute::MULTIPLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
