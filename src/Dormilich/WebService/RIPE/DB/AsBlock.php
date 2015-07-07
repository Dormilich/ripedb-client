<?php
// AsBlock.php

namespace Dormilich\WebService\RIPE\DB;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

class AsBlock extends Object
{
    const PRIMARYKEY = 'as-block';

    /**
     * Create a AS-BLOCK RIPE object.
     * 
     * @param string $value The range of AS numbers in this block.
     * @return self
     */
    public function __construct($value)
    {
        $this->type = self::PRIMARYKEY;
        $this->init();
        $this->setAttribute(self::PRIMARYKEY, $value);
    }

    /**
     * Defines attributes for the AS-BLOCK RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('as-block',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('descr',     Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('remarks',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('org',       Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('notify',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-lower', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',    Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',    Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
