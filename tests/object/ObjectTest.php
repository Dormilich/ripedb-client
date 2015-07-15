<?php

use Dormilich\WebService\RIPE\Attribute as A;
use Dormilich\WebService\RIPE\Object;

class TestObject extends Object
{
	public function __construct()
	{
		$this->setType('foo');
		$this->setKey('bar');
		$this->init();
		$this->setAttribute('source', 'test');
	}

	protected function init()
	{
		$this->create('bar',   A::REQUIRED, A::SINGLE);
		$this->create('abc',   A::OPTIONAL, A::MULTIPLE);
		$this->fixed('choice', A::REQUIRED, ['a', 'b', 'c']);
		$this->matched('num',  A::OPTIONAL, '/\d+/');
		$this->generated('changed');
		// required for serialisation
		$this->create('source', A::REQUIRED, A::SINGLE);
	}
}

class ObjectTest extends PHPUnit_Framework_TestCase
{
	public function testObjectIsTraversable()
	{
		$obj = new TestObject;
		$this->assertInstanceOf('\Traversable', $obj->getIterator());
	}

	public function testObjectTypeIsCorrectlySet()
	{
		$obj = new TestObject;
		$this->assertSame('foo', $obj->getType());
	}

	public function testPrimaryKeyIsCorrectlySet()
	{
		$obj = new TestObject;
		$this->assertSame('bar', $obj->getPrimaryKeyName());
	}

	public function testGetExistingAttribute()
	{
		$obj = new TestObject;

		$this->assertInstanceOf('\Dormilich\WebService\RIPE\Attribute', 
			$obj->getAttribute('bar'));
		$this->assertInstanceOf('\Dormilich\WebService\RIPE\FixedAttribute', 
			$obj->getAttribute('choice'));
		$this->assertInstanceOf('\Dormilich\WebService\RIPE\MatchedAttribute', 
			$obj->getAttribute('num'));
	}

	/**
	 * @expectedException \Dormilich\WebService\RIPE\InvalidAttributeException
	 */
	public function testGetUnknownAttributeFails()
	{
		$obj = new TestObject;
		$obj->getAttribute('12345');
	}

	public function testSetAttributeValue()
	{
		$obj = new TestObject;
		$obj->setAttribute('bar', 'buzz');
		$this->assertSame('buzz', $obj->getAttribute('bar')->getValue());
	}

	public function testSetAttributeValueAsArray()
	{
		$obj = new TestObject;
		$obj['bar'] = 'buzz';
		$this->assertSame('buzz', $obj->getAttribute('bar')->getValue());
	}

	public function testGetPrimaryAttributeValue()
	{
		$obj = new TestObject;
		$obj['bar'] = 'buzz';
		$this->assertSame('buzz', $obj->getPrimaryKey());
	}

	public function testGetAttributeValueAsArray()
	{
		$obj = new TestObject;
		$obj->setAttribute('bar', 'buzz');
		$this->assertSame('buzz', $obj['bar']);
	}

	public function testAttributeCanBeUnset()
	{
		$obj = new TestObject;
		$obj['bar'] = 'buzz';
		unset($obj['bar']);
		$this->assertFalse($obj->getAttribute('bar')->isDefined());
	}

	public function testObjectIsCountable()
	{
		$obj = new TestObject;
		$this->assertSame(1, count($obj));

		$obj['bar'] = 'fizz';
		$this->assertSame(2, count($obj));
	}

	public function testObjectValidity()
	{
		$obj = new TestObject;
		$this->assertFalse($obj->isValid());
		$obj['bar'] = 'foo';
		$obj['choice'] = 'c';
		$this->assertTrue($obj->isValid());
	}

	public function testObjectIsJsonSerialisable()
	{
		$obj = new TestObject;
		$obj['bar'] = 'foo';
		$obj['choice'] = 'c';
		$this->assertNotFalse(json_encode($obj));
	}
}
