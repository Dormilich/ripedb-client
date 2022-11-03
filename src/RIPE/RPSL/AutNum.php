<?php
// AutNum.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

/**
 * Be aware that the 'sponsoring-org' and 'status' attributes
 * must not be set/updated/deleted by the user.
 */
class AutNum extends AbstractObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.102';

    /**
     * Create an AUTONOMOUS NUMBER (AUT-NUM) RIPE object.
     *
     * @param string $value The ASN.
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
        $this->create('aut-num',    Attr::REQUIRED, Attr::SINGLE);
        $this->create('as-name',    Attr::REQUIRED, Attr::SINGLE);
        $this->create('descr',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('member-of',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('import-via', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('import',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mp-import',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('export-via', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('export',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mp-export',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('default',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mp-default', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('remarks',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('org',        Attr::OPTIONAL, Attr::SINGLE);
        $this->create('sponsoring-org', Attr::OPTIONAL, Attr::SINGLE);
        $this->create('admin-c',    Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('tech-c',     Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('abuse-c',    Attr::OPTIONAL, Attr::SINGLE);
        $this->create('notify',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',     Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('source',     Attr::REQUIRED, Attr::SINGLE);

        $this->generated('status');
        $this->generated('created');
        $this->generated('last-modified');
    }
}
