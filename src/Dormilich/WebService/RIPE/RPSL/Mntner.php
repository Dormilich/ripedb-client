<?php
// Mntner.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

class Mntner extends Object
{
    const PRIMARYKEY = 'mntner';

    /**
     * Create a RIPE PERSON object.
     * 
     * @param string $mntner Handle of the maintainer that is represented by this object.
     * @return self
     */
    public function __construct($mntner)
    {
        $this->type = self::PRIMARYKEY;
        $this->init();
        $this->setAttribute(self::PRIMARYKEY, $mntner);
    }

    /**
     * Defines attributes for the PERSON RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('mntner',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('descr',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('org',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('admin-c', Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('tech-c',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('upd-to',  Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('mnt-nfy', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('auth',    Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('remarks', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('notify',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',  Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('abuse-mailbox', Attribute::OPTIONAL, Attribute::MULTIPLE);

        $this->generated('referral-by');
        $this->generated('created');
        $this->generated('last-modified');
    }
}
