<?php
// Poem.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;
use Dormilich\WebService\RIPE\MatchedAttribute;

class Poem extends Object
{
    /**
     * Create a POEM RIPE object.
     * 
     * @param string $title Title of the poem that is represented by this object.
     * @return self
     */
    public function __construct($title)
    {
        $this->setType('poem');
        $this->setKey('poem');
        $this->init();
        $this->setAttribute('poem', $value);
    }

    /**
     * Defines attributes for the POEM RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('poem',    Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('descr',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        // FORM-HAIKU, FORM-LIMERICK, FORM-SONNET-ENGLISH, FORM-PROSE
        $this->matched('form',   Attribute::REQUIRED, '/^FORM-/')
        $this->create('text',    Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('author',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('remarks', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('notify',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('changed', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',  Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
