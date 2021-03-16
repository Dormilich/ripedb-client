<?php

use Dormilich\WebService\RIPE\RPSL\Inetnum;
use Dormilich\WebService\RIPE\RPSL\Role;
use Dormilich\WebService\RIPE\WebService;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class LiveTest extends TestCase
{
	private $ripe;

	public function getRIPE(array $options = array())
	{
		if (NULL === $this->ripe) {
			$this->ripe = new WebService( new Test\Guzzle6Adapter($options) );
		}
		return $this->ripe;
	}

	/**
	 * @group live
	 */
	public function testCreateInetnum()
	{
		$role = new Role;
		$role['role']    = 'inetnum contact';
		$role['address'] = 'Any Street 1';
		$role['e-mail']  = 'no-reply@example.com';
		$role['mnt-by']  = "TEST-DBM-MNT";

		$role = $this->getRIPE()->create($role);

		$ip = new Inetnum('127.0.0.0 - 127.0.0.1');
		$ip['netname'] = "test-ripedb-client";
		$ip['descr']   = "test instance of IPv4 for the ripedb PHP client library";
		$ip['country'] = "DE";
		$ip['admin-c'] = $role->getPrimaryKey();
		$ip['tech-c']  = $role->getPrimaryKey();
		$ip['status']  = "ASSIGNED PA";
		$ip['mnt-by']  = "TEST-DBM-MNT";

		$obj = $this->getRIPE()->create($ip);

		$this->assertInstanceOf('Dormilich\WebService\RIPE\RPSL\Inetnum', $obj);

		return $obj;
	}

	/**
	 * @group live
	 * @depends testCreateInetnum
	 */
	public function testReadInetnum($ip)
	{
		$lookup = new Inetnum($ip->getPrimaryKey());

		$obj = $this->getRIPE()->read($lookup);

		$this->assertEquals($ip['netname'], $obj['netname']);
		$this->assertEquals($ip['descr'],   $obj['descr']);
		$this->assertEquals($ip['country'], $obj['country']);
		$this->assertEquals($ip['admin-c'], $obj['admin-c']);
		$this->assertEquals($ip['tech-c'],  $obj['tech-c']);
		$this->assertEquals($ip['status'],  $obj['status']);
		$this->assertEquals($ip['mnt-by'],  $obj['mnt-by']);
		$this->assertEquals('TEST', $obj['source']);

		return $obj;
	}

	/**
	 * @group live
	 * @depends testReadInetnum
	 */
	public function testUpdateInetnum($ip)
	{
		$ip['country'] = 'US';
		$ip['descr']   = 'updated instance of IPv4 for the ripedb PHP client library';

		$obj = $this->getRIPE()->update($ip);

		$this->assertEquals(['US'], $obj['country']);

		return $obj;
	}

	/**
	 * @group live
	 * @depends testUpdateInetnum
	 */
	public function testVersionsInetnum($ip)
	{
		$versions = $this->getRIPE()->versions($ip);

		$this->assertCount(2, $versions);
		$this->assertArrayHasKey('1', $versions);
		$this->assertArrayHasKey('2', $versions);

		return $ip;
	}

	/**
	 * @group live
	 * @depends testVersionsInetnum
	 */
	public function testVersionInetnum($ip)
	{
		$obj = $this->getRIPE()->version($ip, 2);

		$this->assertEquals($ip, $obj);

		return $ip;
	}

	/**
	 * @group live
	 * @depends testVersionInetnum
	 */
	public function testDeleteInetnum($ip)
	{
        $this->expectException(ClientException::class);

		$this->getRIPE()->delete($ip);

		$role = new Role($ip['admin-c'][0]);
		$this->getRIPE()->delete($role);

		$this->getRIPE()->read($ip);
	}
}
