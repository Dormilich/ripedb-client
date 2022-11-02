<?php
// Inetnum.php

namespace Dormilich\WebService\RIPE\RPSL;

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;

class Inetnum extends AbstractObject
{
    /**
     * The version of the RIPE DB used for attribute definitions.
     */
    const VERSION = '1.102';

    /**
     * Create a INETNUM RIPE object.
     * 
     * Supported input formats:
     *  - IP range string (IP address - space - hyphen - space - IP address)
     *  - IP address/object & IP address/object
     *  - CIDR
     *  - IP address/object & CIDR prefix
     * 
     * @param mixed $address IP range, CIDR, or IP string/object.
     * @param mixed $value CIDR prefix or IP string/object.
     * @return self
     */
    public function __construct($address, $value = null)
    {
        $this->setType('inetnum');
        $this->setKey('inetnum');
        $this->init();
        $this->setAttribute('inetnum', $this->getIPRange($address, $value));
    }

    /**
     * Convert the various input formats to an IP range string. If the input 
     * fails any validation, the address parameter is returned unchanged.
     * 
     * @param mixed $address IP range, CIDR, or IP string/object.
     * @param mixed $value CIDR prefix or IP string/object.
     * @return string IP range string.
     */
    private function getIPRange($address, $value)
    {
        // check for range
        if (strpos($address, '-') !== false) {
            return $address;
        }
        // check for CIDR
        if (strpos($address, '/') !== false)  {
            $cidr = explode('/', $address);
            $range = $this->convertCIDR($cidr[0], $cidr[1]);
            if (!$range) {
                return $address;
            }
            return $range;
        }
        // check for separated CIDR
        if (is_numeric($value)) {
            $range = $this->convertCIDR($address, $value);
            if (!$range) {
                return $address;
            }
            return $range;
        }
        // try input as IP
        if ($value) {
            $start_num = ip2long((string) $address);
            $end_num   = ip2long((string) $value);

            if (false === $start_num or false === $end_num) {
                return $address;
            }

            if ($start_num < $end_num) {
                return long2ip($start_num) . ' - ' . long2ip($end_num);
            } 
            elseif ($start_num > $end_num) {
                return long2ip($end_num) . ' - ' . long2ip($start_num);
            }
            else {
                return long2ip($start_num);
            }
        }

        return (string) $address;
    }

    /**
     * Convert IP and CIDR prefix into an IP range. Returns FALSE if either 
     * input is invalid or the end IP would exceed the IPv4 range.
     * 
     * @param mixed $ip IP address.
     * @param integer $prefix CIDR prefix.
     * @return string IP range or FALSE.
     */
    private function convertCIDR($ip, $prefix)
    {
        $ipnum = ip2long((string) $ip);
        $prefix = filter_var($prefix, \FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 32]
        ]);

        if (false === $ipnum or false === $prefix) {
            return false;
        }

        $netsize = 1 << (32 - $prefix);
        $end_num = $ipnum + $netsize - 1;

        // adjusted so that this works on 32 and 64 bit systems
        $unsignedEndNum = sprintf("%u", $ipnum) + $netsize - 1;
        if ($unsignedEndNum > 4294967295) {
            return false;
        }

        return long2ip($ipnum) . ' - ' . long2ip($end_num);
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
        $this->create('descr',       Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('country',     Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('geofeed',     Attr::OPTIONAL, Attr::SINGLE);
        $this->create('geoloc',      Attr::OPTIONAL, Attr::SINGLE);
        $this->create('language',    Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('org',         Attr::OPTIONAL, Attr::SINGLE);
        $this->create('sponsoring-org', Attr::OPTIONAL, Attr::SINGLE);
        $this->create('admin-c',     Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('tech-c',      Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('abuse-c',     Attr::OPTIONAL, Attr::SINGLE);
        $this->fixed('status',       Attr::REQUIRED, [
            'ALLOCATED UNSPECIFIED', 'ALLOCATED PA',        'ALLOCATED PI', 
            'LIR-PARTITIONED PA',    'LIR-PARTITIONED PI',  'SUB-ALLOCATED PA', 
            'ASSIGNED PA',           'ASSIGNED PI',         'ASSIGNED ANYCAST', 
            'LEGACY',                'NOT_SET',             'EARLY-REGISTRATION',
        ]);
        $this->create('remarks',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',      Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',      Attr::REQUIRED, Attr::MULTIPLE);
        $this->create('mnt-lower',   Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-routes',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-domains', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-irt',     Attr::OPTIONAL, Attr::MULTIPLE);
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
