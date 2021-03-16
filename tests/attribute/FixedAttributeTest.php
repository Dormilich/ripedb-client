<?php

use Dormilich\WebService\RIPE\Exceptions\InvalidValueException;
use Dormilich\WebService\RIPE\FixedAttribute;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;
use PHPUnit\Framework\TestCase;

class FixedAttributeTest extends TestCase
{
	public function testAttributeInterfaceIsImplemented()
	{
		$attr = new FixedAttribute('foo', true, []);
		$this->assertInstanceOf('\Dormilich\WebService\RIPE\AttributeInterface', $attr);
	}

	public function testAttributeClassIsExtended()
	{
		$attr = new FixedAttribute('foo', true, []);
		$this->assertInstanceOf('\Dormilich\WebService\RIPE\Attribute', $attr);
	}

	public function testAttributeIsSingle()
	{
		$attr = new FixedAttribute('foo', true, []);
		$this->assertFalse($attr->isMultiple());
	}

	public function testAttributeRequiredness()
	{
		$attr = new FixedAttribute('foo', Attr::REQUIRED, []);
		$this->assertTrue($attr->isRequired());

		$attr = new FixedAttribute('foo', Attr::OPTIONAL, []);
		$this->assertFalse($attr->isRequired());
	}

	public function testAttributeAcceptsDefinedValues()
	{
		$attr = new FixedAttribute('foo', Attr::REQUIRED, ['a', 'b', 'c']);

		$attr->setValue('a');
		$this->assertSame('a', $attr->getValue());

		$attr->setValue('b');
		$this->assertSame('b', $attr->getValue());

		$attr->setValue('c');
		$this->assertSame('c', $attr->getValue());
	}

	public function testAttributeDoesNotAcceptUndefinedValue()
	{
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[bar]');

        $attr = new FixedAttribute('bar', Attr::REQUIRED, ['a', 'b', 'c']);
		$attr->setValue('x');
	}
}
