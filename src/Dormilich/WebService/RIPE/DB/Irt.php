<?php
// Irt.php

namespace Dormilich\WebService\RIPE\DB;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

class Irt extends Object
{
    const PRIMARYKEY = 'irt';

    /**
     * Create an IRT RIPE object.
     * 
     * @param string $value The name for the response team.
     * @return self
     */
    public function __construct($value)
    {
        $this->type = self::PRIMARYKEY;
        $this->init();
        $this->setAttribute(self::PRIMARYKEY, $value);
    }

    /**
     * Defines attributes for the AUT-NUM RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('irt',        Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('address',    Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('phone',      Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('fax-no',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('e-mail',     Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('signature',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('encryption', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('org',        Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('admin-c',    Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('tech-c',     Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('auth',       Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('remarks',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('irt-nfy',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('notify',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',     Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',     Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
        // deprecated
        $this->generated('abuse-mailbox', Attribute::MULTIPLE);
    }
}
