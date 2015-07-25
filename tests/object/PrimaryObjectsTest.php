<?php

class PrimaryObjectsTest extends PHPUnit_Framework_TestCase
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
}
