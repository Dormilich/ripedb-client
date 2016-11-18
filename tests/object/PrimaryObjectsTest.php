<?php

use PHPUnit\Framework\TestCase;
use Dormilich\WebService\RIPE\RPSL\Route;
use Dormilich\WebService\RIPE\RPSL\Route6;

class PrimaryObjectsTest extends TestCase
{
	public function objectTypeProvider()
	{
		return [
			['AutNum', 		'aut-num'],
			['Domain', 		'domain'], 
			['Inet6num', 	'inet6num'], 
			['Inetnum', 	'inetnum'], 
			['Route', 		'route'], 
			['Route6', 		'route6'], 
			['AsSet', 		'as-set'], 
			['FilterSet', 	'filter-set'], 
			['InetRtr', 	'inet-rtr'], 
			['PeeringSet', 	'peering-set'], 
			['RouteSet', 	'route-set'], 
			['RtrSet', 		'rtr-set'], 
		];
	}

	/**
	 * @dataProvider objectTypeProvider
	 */
	public function testObjectTypeAndKey($class, $type)
	{
		$class = 'Dormilich\\WebService\\RIPE\\RPSL\\'.$class;
		$obj = new $class('123');
		$this->assertEquals($type, $obj->getType());
		$this->assertEquals('123', $obj->getPrimaryKey());
	}

	public function routeKeyProvider()
	{
		return [
			['192.168.2.0/24',        '192.168.2.0/24',       '192.168.2.0/24', null],
			['192.168.2.0/24AS1943',  '192.168.2.0/24AS1943', '192.168.2.0/24', 'AS1943'],
			['192.168.2.0/24 AS1943', '192.168.2.0/24AS1943', '192.168.2.0/24', 'AS1943'],
			['AS1943 192.168.2.0/24', '192.168.2.0/24AS1943', '192.168.2.0/24', 'AS1943'],
		];
	}

	/**
	 * @dataProvider routeKeyProvider
	 */
	public function testRouteKey($input, $key, $route, $origin)
	{
		$obj = new Route($input);
		$this->assertEquals($key, $obj->getPrimaryKey());
		$this->assertEquals($route, $obj['route']);
		$this->assertEquals($origin, $obj['origin']);
	}

	public function route6KeyProvider()
	{
		return [
			['2001:db8:8d3::/48',        '2001:db8:8d3::/48',       '2001:db8:8d3::/48', null],
			['2001:db8:8d3::/48AS1943',  '2001:db8:8d3::/48AS1943', '2001:db8:8d3::/48', 'AS1943'],
			['2001:db8:8d3::/48 AS1943', '2001:db8:8d3::/48AS1943', '2001:db8:8d3::/48', 'AS1943'],
			['AS1943 2001:db8:8d3::/48', '2001:db8:8d3::/48AS1943', '2001:db8:8d3::/48', 'AS1943'],
		];
	}

	/**
	 * @dataProvider route6KeyProvider
	 */
	public function testRoute6Key($input, $key, $route, $origin)
	{
		$obj = new Route6($input);
		$this->assertEquals($key, $obj->getPrimaryKey());
		$this->assertEquals($route, $obj['route6']);
		$this->assertEquals($origin, $obj['origin']);
	}
}
