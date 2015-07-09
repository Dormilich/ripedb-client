<?php
// KeyCert.php

namespace Dormilich\WebService\RIPE\DB\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;

/**
 * Be aware that the 'method', 'owner' and 'fingerpr' attributes 
 * must not be set/updated/deleted by the user.
 */
class KeyCert extends Object
{
    const PRIMARYKEY = 'key-cert';

    /**
     * Create a key certification (KEY-CERT) RIPE object.
     * 
     * @param string $value The key ID.
     * @return self
     */
    public function __construct($value)
    {
        $this->type = self::PRIMARYKEY;
        $this->init();
        $this->setAttribute(self::PRIMARYKEY, $value);
    }

    /**
     * Defines attributes for the KEY-CERT RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('key-cert', Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('certif',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('org',      Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('remarks',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('notify',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('admin-c',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('tech-c',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',   Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('method');
        $this->generated('owner', Attribute::MULTIPLE);
        $this->generated('fingerpr');
        $this->generated('created');
        $this->generated('last-modified');
    }
}
