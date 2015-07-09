<?php
// RouteSet.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

class RouteSet extends Object
{
    const PRIMARYKEY = 'route-set';

    /**
     * Create a ROUTE-SET RIPE object.
     * 
     * @param string $value The name of the set (of route prefixes).
     * @return self
     */
    public function __construct($value)
    {
        $this->type = self::PRIMARYKEY;
        $this->init();
        $this->setAttribute(self::PRIMARYKEY, $value);
    }

    /**
     * Defines attributes for the ROUTE-SET RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('route-set',   Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('descr',       Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('members',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mp-members',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mbrs-by-ref', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('remarks',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('org',         Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('tech-c',      Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('admin-c',     Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('notify',      Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',      Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('mnt-lower',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('changed',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',      Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
