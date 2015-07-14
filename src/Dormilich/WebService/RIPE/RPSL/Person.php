<?php
// Person.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;
use Dormilich\WebService\RIPE\MatchedAttribute;

class Person extends Object
{
    /**
     * Create a PERSON RIPE object.
     * 
     * @param string $value NIC handle. If not specified an auto-handle is used.
     * @return self
     */
    public function __construct($value = 'AUTO-1')
    {
        $this->setType('person');
        $this->setKey('nic-hdl');
        $this->init();
        $this->setAttribute('nic-hdl', $value);
    }

    /**
     * Defines attributes for the PERSON RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('person',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('address', Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('phone',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('fax-no',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('e-mail',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('org',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        // auto: AUTO-1, AUTO-1{[A-Z]+}, manual: {[A-Z]+}, {[A-Z]+}-RIPE, {[A-Z]+}-{2-letter country code}
        $this->create('nic-hdl', Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('remarks', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('notify',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',  Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',  Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
        // deprecated
        $this->generated('abuse-mailbox', Attribute::MULTIPLE);
    }
}
