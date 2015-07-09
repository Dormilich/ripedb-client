<?php
// PoeticForm.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

class PoeticForm extends Object
{
    const PRIMARYKEY = 'poetic-form';

    /**
     * Create a POETIC-FORM RIPE object.
     * 
     * @param string $value The name of the genre.
     * @return self
     */
    public function __construct($value)
    {
        $this->type = self::PRIMARYKEY;
        $this->init();
        $this->setAttribute(self::PRIMARYKEY, $value);
    }

    /**
     * Defines attributes for the POETIC-FORM RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->attributes['poetic-form'] = new MatchedAttribute('poetic-form', Attribute::REQUIRED, '/^FORM-/');

        $this->create('descr',       Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('admin-c',     Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('remarks',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('notify',      Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',      Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',      Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
