<?php
// Person.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\RipeObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Person extends RipeObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.92';

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
        $this->create('person',  Attr::REQUIRED, Attr::SINGLE);
        $this->create('address', Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('phone',   Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('fax-no',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('e-mail',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('org',     Attr::OPTIONAL, Attr::MULTIPLE);
        // auto: AUTO-1, AUTO-1{[A-Z]+}, manual: {[A-Z]+}, {[A-Z]+}-RIPE, {[A-Z]+}-{2-letter country code}
        $this->create('nic-hdl', Attr::REQUIRED, Attr::SINGLE);
        $this->create('remarks', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',  Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('source',  Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
