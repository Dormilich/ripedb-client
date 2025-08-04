<?php
// Mntner.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Mntner extends AbstractObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.118';

    /**
     * Create a maintainer (MNTNER) RIPE object.
     *
     * @param string $value Handle of the maintainer that is represented by this object.
     */
    public function __construct($value)
    {
        $this->setType('mntner');
        $this->setKey('mntner');
        $this->init();
        $this->setAttribute('mntner', $value);
    }

    /**
     * Defines attributes for the MNTNER RIPE object.
     *
     * @return void
     */
    protected function init()
    {
        $this->create('mntner',  Attr::REQUIRED, Attr::SINGLE);
        $this->create('descr',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('org',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('admin-c', Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('tech-c',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('upd-to',  Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('mnt-nfy', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('auth',    Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('remarks', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',  Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('mnt-ref', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('source',  Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
