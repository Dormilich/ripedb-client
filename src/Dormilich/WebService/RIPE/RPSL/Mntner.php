<?php
// Mntner.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Mntner extends Object
{
    /**
     * Create a maintainer (MNTNER) RIPE object.
     * 
     * @param string $mntner Handle of the maintainer that is represented by this object.
     * @return self
     */
    public function __construct($mntner)
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
        $this->create('descr',   Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('org',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('admin-c', Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('tech-c',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('upd-to',  Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('mnt-nfy', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('auth',    Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('remarks', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',  Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('changed', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('source',  Attr::REQUIRED, Attr::SINGLE);
        $this->create('abuse-mailbox', Attr::OPTIONAL, Attr::MULTIPLE);

        $this->generated('referral-by');
        $this->generated('created');
        $this->generated('last-modified');
    }
}
