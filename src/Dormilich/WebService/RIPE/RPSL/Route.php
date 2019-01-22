<?php
// Route.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\RipeObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Route extends RipeObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.92';

    /**
     * Create a ROUTE RIPE object.
     * 
     * @param string $value The IPv4 address prefix of the route.
     *      Forms a combined primary key with the 'origin' attribute.
     * @return self
     */
    public function __construct($value)
    {
        $this->setType('route');
        $this->init();
        $this->parseKey($value);
    }

    /**
     * Parse input for a composite primary key.
     * 
     * @param string $value Route with optional Aut-Num.
     * @return void
     */
    private function parseKey($value)
    {
        if (preg_match('/AS\d+/', $value, $match) === 1) {
            $this->setAttribute('origin', $match[0]);
            $value = str_replace($match[0], '', $value);
        }

        $this->setAttribute('route', trim($value));
        $this->setKey('route');
    }

    /**
     * Get the value of the attributes defined as (composite) primary key.
     * 
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->getAttribute('route')->getValue() 
             . $this->getAttribute('origin')->getValue();
    }

    /**
     * Defines attributes for the ROUTE RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('route',        Attr::REQUIRED, Attr::SINGLE);
        $this->create('descr',        Attr::OPTIONAL, Attr::MULTIPLE);
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
        $this->create('source',       Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
