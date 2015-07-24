<?php
// Inetnum.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Inetnum extends Object
{
    /**
     * Create a INETNUM RIPE object
     * 
     * @param string $netnum A range of or a single IPv4 address.
     * @return self
     */
    public function __construct($netnum)
    {
        $this->setType('inetnum');
        $this->setKey('inetnum');
        $this->init();
        $this->setAttribute('inetnum', $value);
    }

    /**
     * Defines attributes for the INETNUM RIPE object. 
     * 
     * @return void
     */
    protected function init()
    {
        $this->create('inetnum',     Attr::REQUIRED, Attr::SINGLE);
        $this->create('netname',     Attr::REQUIRED, Attr::SINGLE);
        $this->create('descr',       Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('country',     Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('geoloc',      Attr::OPTIONAL, Attr::SINGLE);
        $this->create('language',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('org',         Attr::OPTIONAL, Attr::SINGLE);
        $this->generated('sponsoring-org');
        $this->create('admin-c',     Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('tech-c',      Attr::REQUIRED, Attr::MULTIPLE);
        $this->fixed('status',       Attr::REQUIRED, [
            'ALLOCATED UNSPECIFIED', 'ALLOCATED PA',        'ALLOCATED PI', 
            'LIR-PARTITIONED PA',    'LIR-PARTITIONED PI',  'SUB-ALLOCATED PA', 
            'ASSIGNED PA',           'ASSIGNED PI',         'ASSIGNED ANYCAST', 
            'LEGACY',                'NOT_SET', 
        ]);
        $this->create('remarks',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',      Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('mnt-lower',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-routes',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-domains', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-irt',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('changed',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('source',      Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }
}
/*
 * The allowed values for the 'status' attribute:
 * 
 * ‘ALLOCATED UNSPECIFIED’ 
 *      This is mostly used to identify blocks of addresses for which the 
 *      RIPE NCC is administratively responsible. Historically, a small 
 *      number of allocations made to members have this status also.
 * ‘ALLOCATED PA’ 
 *      These are allocations made to members by the RIPE NCC.
 * ‘ALLOCATED PI’ 
 *      This is mostly used to identify blocks of addresses from which the 
 *      RIPE NCC makes assignments to end users. Historically, a small number 
 *      of allocations made to members have this status also.
 * ‘LIR-PARTITIONED PA’ 
 *      This is to allow partitioning of an allocation by a member for 
 *      internal business reasons.
 * ‘LIR-PARTITIONED PI’ 
 *      This is to allow partitioning of an allocation by a member for 
 *      internal business reasons.
 * ‘SUB-ALLOCATED PA’ 
 *      A member can sub-allocate a part of an allocation to another 
 *      organisation. The other organisation may take over some of the 
 *      management of this sub-allocation. However, the RIPE NCC member 
 *      is still responsible for the whole of their registered resources, 
 *      even if parts of it have been sub-allocated. Provisions have been 
 *      built in to the RIPE Database software to ensure that the member 
 *      is always technically in control of their allocated address space.
 * ‘ASSIGNED PA’ 
 *      These are assignments made by a member from their allocations to an 
 *      End User.
 * ‘ASSIGNED PI’ 
 *      These are assignments made by the RIPE NCC directly to an End User. 
 *      In most cases, there is a member acting as the sponsoring organisation 
 *      who handles the administrative processes on behalf of the End User. 
 *      The sponsoring organisation may also manage the resource and related 
 *      objects in the RIPE Database for the End User.
 * ‘ASSIGNED ANYCAST’ 
 *      This address space has been assigned for use in TLD anycast networks.
 * ‘LEGACY’ 
 *      These are resources that were allocated to users before the RIPE NCC 
 *      was set up.
 * ‘NOT-SET’ 
 *      There are some very old objects in the RIPE Database where the status 
 *      was unknown when the “status:” attribute was introduced. When it became 
 *      a mandatory attribute, these objects were given this status value. 
 *      When contact is made with the organisations holding these resources, 
 *      the real status value will be determined. 
 */
