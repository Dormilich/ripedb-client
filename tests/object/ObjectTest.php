<?php

use Dormilich\WebService\RIPE\Object;
use Test\TestObject;

class ObjectTest extends PHPUnit_Framework_TestCase
{
	// testing the ObjectInterface implementation (1)
	// these tests don’t need an attribute value set

	public function testObjectInterfaceIsImplemented()
	{
		$obj = new TestObject;
		$this->assertInstanceOf('\Dormilich\WebService\RIPE\ObjectInterface', $obj);
	}

	public function testObjectTypeIsCorrectlySet()
	{
		$obj = new TestObject;
		$this->assertSame('foo', $obj->getType());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testSetEmptyObjectTypeFails()
	{
		new TestObject(NULL);
	}

	public function testPrimaryKeyIsCorrectlySet()
	{
		$obj = new TestObject;
		$this->assertSame('bar', $obj->getPrimaryKeyName());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testSetEmptyObjectKeyFails()
	{
		new TestObject('foo', NULL);
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

	public function testSetSingleAttributeValue()
	{
		$obj = new TestObject;
		$obj->setAttribute('bar', 'buzz');
		$this->assertSame('buzz', $obj->getAttribute('bar')->getValue());
	}

	// testing the ArrayAccess implementation
	// these tests rely on getAttribute()

	public function testSetAttributeValueAsArray()
	{
		$obj = new TestObject;
		$obj['bar'] = 'buzz';
		$this->assertSame('buzz', $obj->getAttribute('bar')->getValue());
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

	public function testAttributeExistence()
	{
		$obj = new TestObject;
		$this->assertTrue(isset($obj['bar']));
		$this->assertFalse(isset($obj['xyz']));
	}

	// testing Countable implementation

	public function testObjectIsCountable()
	{
		$obj = new TestObject;
		$this->assertSame(1, count($obj));

		$obj['bar'] = 'fizz';
		$this->assertSame(2, count($obj));
	}

	// testing JsonSerialisable implementation

	public function testObjectIsJsonSerialisable()
	{
		$obj = new TestObject;
		$obj['bar'] = 'foo';
		$obj['choice'] = 'c';
		$this->assertNotFalse(json_encode($obj));
	}

	// testing IteratorAggregate implementation

	public function testObjectIsTraversable()
	{
		$obj = new TestObject;
		$this->assertInstanceOf('\Traversable', $obj->getIterator());
	}

	// testing the ObjectInterface implementation (2)
	// these tests require set attributes

	public function testGetPrimaryAttributeValue()
	{
		$obj = new TestObject;
		$obj['bar'] = 'buzz';
		$this->assertSame('buzz', $obj->getPrimaryKey());
	}

	public function testObjectValidity()
	{
		$obj = new TestObject;
		$this->assertFalse($obj->isValid());
		$obj['bar'] = 'foo';
		$obj['choice'] = 'c';
		$this->assertTrue($obj->isValid());
	}

	public function testObjectAddAttributeValues()
	{
		$obj = new TestObject;
		$obj->setAttribute('abc', 'x');
		$this->assertEquals(['x'], $obj['abc']);
		$obj->addAttribute('abc', 'y');
		$this->assertEquals(['x', 'y'], $obj['abc']);
	}

	public function testObjectToArray()
	{
		$obj = new TestObject;
		$obj
			->addAttribute('bar', 'bar')
			->addAttribute('abc', 'x')
			->addAttribute('abc', 'y')
			->addAttribute('abc', 'z')
			->addAttribute('num', 1)
			->addAttribute('choice', 'c')
			->addAttribute('source', 'test')
		;
		$array = $obj->toArray();

		$ref = json_decode(file_get_contents(__DIR__ . '/_fixtures/test.json'), true);
		$this->assertEquals($ref, $array);
	}

	/**
	 * @expectedException \Dormilich\WebService\RIPE\IncompleteRIPEObjectException
	 */
	public function testIncompleteObjectToArrayFails()
	{
		$obj = new TestObject;
		$this->assertFalse($obj->isValid());
		$obj->toArray();
	}

	public function testObjectToXML()
	{
		$obj = new TestObject;
		$obj
			->addAttribute('bar', 'bar')
			->addAttribute('abc', 'x')
			->addAttribute('abc', 'y')
			->addAttribute('abc', 'z')
			->addAttribute('num', 1)
			->addAttribute('choice', 'c')
			->addAttribute('source', 'test')
		;
		$xml = $obj->toXML();

		$this->assertSame('test', (string) $xml->objects->object->source['id']);

		$ref = simplexml_load_file(__DIR__ . '/_fixtures/test.xml');
		$this->assertEquals($ref, $xml);
	}

	/**
	 * @expectedException \Dormilich\WebService\RIPE\IncompleteRIPEObjectException
	 */
	public function testIncompleteObjectToXMLFails()
	{
		$obj = new TestObject;
		$this->assertFalse($obj->isValid());
		$obj->toXML();
	}
}
