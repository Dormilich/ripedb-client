<?php
// RouteSet.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class RouteSet extends AbstractObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.112';

    /**
     * Create a ROUTE-SET RIPE object.
     *
     * @param string $value The name of the set (of route prefixes).
     */
    public function __construct($value)
    {
        $this->setType('route-set');
        $this->setKey('route-set');
        $this->init();
        $this->setAttribute('route-set', $value);
    }

    /**
     * Defines attributes for the ROUTE-SET RIPE object.
     *
     * @return void
     */
    protected function init()
    {
        $this->create('route-set',   Attr::REQUIRED, Attr::SINGLE);
        $this->create('descr',       Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('members',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mp-members',  Attr::OPTIONAL, Attr::MULTIPLE);
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
