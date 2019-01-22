<?php
// Poem.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\RipeObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Poem extends RipeObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.92';

    /**
     * Create a POEM RIPE object.
     * 
     * @param string $value Title of the poem that is represented by this object.
     * @return self
     */
    public function __construct($value)
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
        $this->create('poem',    Attr::REQUIRED, Attr::SINGLE);
        $this->create('descr',   Attr::OPTIONAL, Attr::MULTIPLE);
        // FORM-HAIKU, FORM-LIMERICK, FORM-SONNET-ENGLISH, FORM-PROSE
        $this->matched('form',   Attr::REQUIRED, '/^FORM-/');
        $this->create('text',    Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('author',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('remarks', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',  Attr::REQUIRED, Attr::SINGLE);
        $this->create('source',  Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
