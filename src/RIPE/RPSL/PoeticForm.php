<?php
// PoeticForm.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class PoeticForm extends AbstractObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.112';

    /**
     * Create a POETIC-FORM RIPE object.
     *
     * @param string $value The name of the genre.
     */
    public function __construct($value)
    {
        $this->setType('poetic-form');
        $this->setKey('poetic-form');
        $this->init();
        $this->setAttribute('poetic-form', $value);
    }

    /**
     * Defines attributes for the POETIC-FORM RIPE object.
     *
     * @return void
     */
    protected function init()
    {
        $this->matched('poetic-form', Attr::REQUIRED, '/^FORM-/');
        $this->create('descr',        Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('admin-c',      Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('remarks',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',       Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',       Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('source',       Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
