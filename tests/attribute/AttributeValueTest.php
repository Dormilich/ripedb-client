<?php

use Dormilich\WebService\RIPE\Attribute;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;
use Dormilich\WebService\RIPE\AttributeValue;
use Dormilich\WebService\RIPE\FixedAttribute;
use Dormilich\WebService\RIPE\MatchedAttribute;
use Test\RegObject;

class AttributeValueTest extends PHPUnit_Framework_TestCase
{
	public function testAttributeAcceptsValueObject()
	{
		$reg   = new RegObject;
		$value = new AttributeValue('something');

		$this->assertSame('something', $value->getValue());

		$reg['register'] = $value;

		$this->assertEquals('something',  $reg['register']);
		$this->assertNotSame('something', $reg['register']);
	}

	public function testAttributeValueWithComment()
	{
		$reg   = new RegObject;
		$value = new AttributeValue('something');
		$value->setComment('else');

		$this->assertSame('else', $value->getComment());

		$reg['register'] = $value;

		$this->assertEquals('something # else', $reg['register']);
	}

	public function testAttributeValueWithReference()
	{
		$reg   = new RegObject;
		$value = new AttributeValue('something');
		$value->setType('poem');

		$reg['register'] = $value;

		$this->assertEquals('something', $reg['register']);

		$poem = $reg['register']->getObject();
		$this->assertInstanceOf('Dormilich\WebService\RIPE\RPSL\Poem', $poem);
		$this->assertSame('something', $poem->getPrimaryKey());
	}

	/**
	 * @expectedException Dormilich\WebService\RIPE\Exceptions\InvalidDataTypeException
	 */
	public function testGetObjectWithoutTypeFails()
	{
		$value = new AttributeValue('something');
		$value->getObject();
	}

	/**
	 * @expectedException Dormilich\WebService\RIPE\Exceptions\InvalidValueException
	 */
	public function testGetObjectWithUnknownTypeFails()
	{
		$value = new AttributeValue('something');
		$value->setType('foo')->getObject();
	}

	public function testAttributeValueWithLink()
	{
		$reg   = new RegObject;
		$value = new AttributeValue('something');
		$link  = 'http://www.example.com/something';
		$value->setLink($link);

		$reg['register'] = $value;

		$this->assertEquals($link, $reg['register']->getLink());
	}

	public function testFixedAttributeWithValueObject()
	{
		$attr  = new FixedAttribute('foo', Attr::REQUIRED, ['a', 'b', 'c']);
		$value = new AttributeValue('a');

		$attr->setValue($value);
		$this->assertSame('a', $attr->getValue());
	}

	public function testMatchedAttributeWithValueObject()
	{
		$attr  = new MatchedAttribute('foo', Attr::REQUIRED, '/x/');
		$value = new AttributeValue('xyz');

		$attr->setValue($value);
		$this->assertSame('xyz', $attr->getValue());
	}
}
