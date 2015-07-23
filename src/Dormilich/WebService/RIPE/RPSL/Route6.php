<?php
// Route6.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Route6 extends Object
{
    /**
     * Create a ROUTE6 RIPE object.
     * 
     * @param string $value The IPv6 address prefix of the route.
     *      Forms a combined primary key with the 'origin' attribute.
     * @return self
     */
    public function __construct($value)
    {
        $this->setType('route6');
        $this->setKey('route6');
        $this->init();
        $this->setAttribute('route6', $value);
    }

    /**
     * Defines attributes for the ROUTE6 RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('route6',       Attr::REQUIRED, Attr::SINGLE);
        $this->create('descr',        Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('origin',       Attr::REQUIRED, Attr::SINGLE);
        $this->create('pingable',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('ping-hdl',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('holes',        Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('org',          Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('member-of',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('inject',       Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('aggr-mtd',     Attr::OPTIONAL, Attr::SINGLE);
        $this->create('aggr-bndry',   Attr::OPTIONAL, Attr::SINGLE);
        $this->create('export-comps', Attr::OPTIONAL, Attr::SINGLE);
        $this->create('components',   Attr::OPTIONAL, Attr::SINGLE);
        $this->create('remarks',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',       Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-lower',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-routes',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',       Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('changed',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('source',       Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
