<?php
// Irt.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Irt extends AbstractObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.102';

    /**
     * Create an incident response team (IRT) RIPE object.
     *
     * @param string $value The name for the response team.
     */
    public function __construct($value)
    {
        $this->setType('irt');
        $this->setKey('irt');
        $this->init();
        $this->setAttribute('irt', $value);
    }

    /**
     * Defines attributes for the IRT RIPE object.
     *
     * @return void
     */
    protected function init()
    {
        $this->create('irt',        Attr::REQUIRED, Attr::SINGLE);
        $this->create('address',    Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('phone',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('fax-no',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('e-mail',     Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('signature',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('encryption', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('org',        Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('admin-c',    Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('tech-c',     Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('auth',       Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('remarks',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('irt-nfy',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',     Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('source',     Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
