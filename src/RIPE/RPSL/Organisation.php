<?php
// Organisation.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Organisation extends AbstractObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.118';

    /**
     * Create an ORGANISATION RIPE object.
     *
     * @param string $value A letter combination appended to the Auto-ID.
     */
    public function __construct($value = 'AUTO-1')
    {
        $this->setType('organisation');
        $this->setKey('organisation');
        $this->init();
        $this->setAttribute('organisation', $value);
    }

    /**
     * Defines attributes for the ORGANISATION RIPE object.
     *
     * @return void
     */
    protected function init()
    {
        $this->create('organisation', Attr::REQUIRED, Attr::SINGLE);
        $this->create('org-name',     Attr::REQUIRED, Attr::SINGLE);
        $this->fixed('org-type',      Attr::REQUIRED, [
            'IANA', 'RIR', 'NIR', 'LIR', 'WHITEPAGES', 'DIRECT ASSIGNMENT', 'OTHER',
        ]);
        $this->create('descr',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('remarks',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('address',  Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('country',  Attr::OPTIONAL, Attr::SINGLE);
        $this->create('phone',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('fax-no',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('e-mail',   Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('geoloc',   Attr::OPTIONAL, Attr::SINGLE);
        $this->create('language', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('org',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('admin-c',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('tech-c',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('abuse-c',  Attr::OPTIONAL, Attr::SINGLE);
        $this->create('ref-nfy',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-ref',  Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('notify',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',   Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('source',   Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
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
