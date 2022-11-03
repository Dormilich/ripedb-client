<?php
// AsSet.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class AsSet extends AbstractObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.104';

    /**
     * Create an AS-SET RIPE object.
     *
     * @param string $value The name of the AS-Set.
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
        $this->create('as-set',      Attr::REQUIRED, Attr::SINGLE);
        $this->create('descr',       Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('members',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mbrs-by-ref', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('remarks',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('org',         Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('tech-c',      Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('admin-c',     Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('notify',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',      Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('mnt-lower',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('source',      Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
