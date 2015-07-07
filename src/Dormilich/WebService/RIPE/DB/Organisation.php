<?php
// Organisation.php

namespace Dormilich\WebService\RIPE\DB;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\Attribute;
use Dormilich\WebService\RIPE\FixedAttribute;

class Organisation extends Object
{
    const PRIMARYKEY = 'organisation';

    /**
     * Create an ORGANISATION RIPE object.
     * 
     * @param string $value A letter combination appended to the Auto-ID.
     * @return self
     */
    public function __construct($value = '')
    {
        $this->type = self::PRIMARYKEY;
        $this->init();
        $this->setAttribute(self::PRIMARYKEY, 'AUTO-1' . $value);
    }

    /**
     * Defines attributes for the AUT-NUM RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('organisation', Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('org-name', Attribute::REQUIRED, Attribute::SINGLE);

        $this->attributes['org-type'] = new FixedAttribute('org-type', Attribute::REQUIRED, [
            'IANA', 'RIR', 'NIR', 'LIR', 'WHITEPAGES', 'DIRECT ASSIGNMENT', 'OTHER', 
        ]);

        $this->create('descr',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('remarks',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('address',  Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('phone',    Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('fax-no',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('e-mail',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('geoloc',   Attribute::OPTIONAL, Attribute::SINGLE);
        $this->create('language', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('org',      Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('admin-c',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('tech-c',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        // if set it must reference a ROLE object with the 'abuse-mailbox' attribute
        $this->create('abuse-c',  Attribute::OPTIONAL, Attribute::SINGLE);
        $this->create('ref-nfy',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-ref',  Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('notify',   Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',   Attribute::REQUIRED, Attribute::MULTIPLE);
        $this->create('changed',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',   Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
        // deprecated
        $this->generated('abuse-mailbox', Attribute::MULTIPLE);
    }
}

/**
 * The allowed values for the 'org-type' attribute:
 * 
 * Users can only create organisation objects with the type ‘OTHER’. 
 * The rest of the values can only be set by the RIPE NCC.
 *
 * 'IANA'
 *      Only used for Internet Assigned Numbers Authority
 * 'RIR'
 *      Only used for the five Regional Internet Registries
 * 'NIR'
 *      This is for National Internet Registries (there are no NIRs in the 
 *      RIPE NCC service region, but it is used by APNIC)
 * 'LIR'
 *      This represents all the Local Internet Registries (the RIPE NCC members)
 * 'WHITEPAGES'
 *      A little-used historical idea for people who have a ‘significant’ presence 
 *      in the industry but who don’t manage any resources in the RIPE Database.
 * 'DIRECT_ASSIGNMENT'
 *      Used for organisations who have a direct contract with RIPE NCC
 * 'OTHER'
 *      This represents all organisations that do not fit any of the above categories.
 */
