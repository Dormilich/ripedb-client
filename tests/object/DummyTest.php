<?php

use Dormilich\WebService\RIPE\Dummy;

class DummyTest extends PHPUnit_Framework_TestCase
{
	public function testObjectTypeIsCorrectlySet()
	{
		$obj = new Dummy('foo', 'bar');
		$this->assertSame('foo', $obj->getType());
		$this->assertSame('bar', $obj->getPrimaryKeyName());
	}
}
