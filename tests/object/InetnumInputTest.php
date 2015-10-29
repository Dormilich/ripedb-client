<?php

use Dormilich\WebService\RIPE\RPSL\Inetnum;
use Test\IP;

class InetnumInputTest extends PHPUnit_Framework_TestCase
{
    public function testRange()
    {
        $range = '73.46.254.16 - 73.46.254.31';
        $net = new Inetnum($range);

        $this->assertSame($range, $net->getPrimaryKey());
    }

    public function testCIDR()
    {
        $range = '73.46.254.16 - 73.46.254.31';
        $net = new Inetnum('73.46.254.16/28');

        $this->assertSame($range, $net->getPrimaryKey());
    }

    public function testIpAndPrefix()
    {
        $range = '73.46.254.16 - 73.46.254.31';
        $net = new Inetnum('73.46.254.16', '28');

        $this->assertSame($range, $net->getPrimaryKey());
    }

    public function testIpAndInvalidPrefix()
    {
        $range = '73.46.254.16 - 73.46.254.31';
        $net = new Inetnum('73.46.254.16', 50);

        $this->assertSame('73.46.254.16', $net->getPrimaryKey());
    }

    public function testObjectAndPrefix()
    {
        $range = '73.46.254.16 - 73.46.254.31';
        $net = new Inetnum(new IP('73.46.254.16'), '28');

        $this->assertSame($range, $net->getPrimaryKey());
    }

    public function testIpAndIp()
    {
        $range = '73.46.254.16 - 73.46.254.31';
        $net = new Inetnum('73.46.254.16', '73.46.254.31');

        $this->assertSame($range, $net->getPrimaryKey());
    }

    public function testIpAndIpWithSwitchedPositions()
    {
        $range = '73.46.254.16 - 73.46.254.31';
        $net = new Inetnum('73.46.254.31', '73.46.254.16');

        $this->assertSame($range, $net->getPrimaryKey());
    }

    public function testIpAsObject()
    {
        $net = new Inetnum(new IP('73.46.254.16'));

        $this->assertSame('73.46.254.16', $net->getPrimaryKey());
    }

    public function testIpAndIpAsObjects()
    {
        $range = '73.46.254.16 - 73.46.254.31';
        $net = new Inetnum(new IP('73.46.254.16'), new IP('73.46.254.31'));

        $this->assertSame($range, $net->getPrimaryKey());
    }

    public function testInvalidInputIsRetained()
    {
        $bogus = 'example.com';
        $net1 = new Inetnum($bogus);
        // as Ip & IP
        $net2 = new Inetnum($bogus, '127.0.0.1');
        // as CIDR
        $net3 = new Inetnum($bogus, 30);

        $this->assertSame($bogus, $net1->getPrimaryKey());
        $this->assertSame($bogus, $net2->getPrimaryKey());
        $this->assertSame($bogus, $net3->getPrimaryKey());
    }
}
