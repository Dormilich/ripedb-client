<?php

use Dormilich\WebService\RIPE\Attribute;

class StringObject
{
	public function __toString()
	{
		return 'test';
	}
}

class AttributeTest extends PHPUnit_Framework_TestCase
{
	public function testAttributeInterfaceIsImplemented()
	{
		$attr = new Attribute('foo', true, true);
		$this->assertInstanceOf('\Dormilich\WebService\RIPE\AttributeInterface', $attr);
	}

	public function testAttributeHasCorrectName()
	{
		$attr = new Attribute('foo', true, true);
		$this->assertSame('foo', $attr->getName());

		$attr = new Attribute(1.8, true, true);
		$this->assertSame('1.8', $attr->getName());
	}

	public function testAttributeIsEmptyByDefault()
	{
		$attr = new Attribute('foo', true, true);
		$this->assertFalse($attr->isDefined());
	}

	public function constructorPropertyProvider()
	{
		return [
			[true,  true, true,  true], [true,  false, true,  false], 
			[false, true, false, true], [false, false, false, false], 
			[0,     1,    false, true], ['x',   NULL,  true,  false],
			[Attribute::REQUIRED, Attribute::SINGLE,   true,  false],
			[Attribute::OPTIONAL, Attribute::MULTIPLE, false, true],
		];
	}

	/**
	 * @dataProvider constructorPropertyProvider
	 */
	public function testAttributeHasCorrectPropertiesSet($required, $multiple, $expect_required, $expect_multiple)
	{
		$attr = new Attribute('foo', $required, $multiple);

		$this->assertSame($expect_required, $attr->isRequired());
		$this->assertSame($expect_multiple, $attr->isMultiple());
	}

	public function testAttributeConvertsInputToStrings()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::SINGLE);

		$attr->setValue(1);
		$this->assertSame('1', $attr->getValue());

		$attr->setValue(2.718);
		$this->assertSame('2.718', $attr->getValue());

		$attr->setValue('bar');
		$this->assertSame('bar', $attr->getValue());

		$test = new StringObject;
		$attr->setValue($test);
		$this->assertSame('test', $attr->getValue());

		// I am not aware that the RIPE DB uses booleans somewhere…
		$attr->setValue(true);
		$this->assertSame('true', $attr->getValue());

		$attr->setValue(false);
		$this->assertSame('false', $attr->getValue());
	}

	/**
	 * @expectedException \Dormilich\WebService\RIPE\InvalidDataTypeException
	 */
	public function testAttributeDoesNotAcceptNullValue()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::SINGLE);
		$attr->setValue(NULL);
	}

	/**
	 * @expectedException \Dormilich\WebService\RIPE\InvalidDataTypeException
	 */
	public function testAttributeDoesNotAcceptResource()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::SINGLE);
		$attr->setValue(tmpfile());
	}

	/**
	 * @expectedException \Dormilich\WebService\RIPE\InvalidDataTypeException
	 */
	public function testAttributeDoesNotAcceptObject()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::SINGLE);
		$attr->setValue(new stdClass);
	}

	public function testSingleAttributeOnlyHasOneValue()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::SINGLE);

		$attr->setValue('fizz');
		$this->assertSame('fizz', $attr->getValue());

		$attr->setValue('buzz');
		$this->assertSame('buzz', $attr->getValue());

		$attr->addValue('bar');
		$this->assertSame('bar', $attr->getValue());
	}

	/**
	 * @expectedException \Dormilich\WebService\RIPE\InvalidDataTypeException
	 */
	public function testSingleAttributeDoesNotAllowArrayInput()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::SINGLE);
		$attr->setValue(['fizz', 'buzz']);
	}

	public function testMultipleAttributeReturnsList()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::MULTIPLE);

		$attr->addValue('fizz');
		$this->assertSame(['fizz'], $attr->getValue());

		$attr->addValue('buzz');
		$this->assertSame(['fizz', 'buzz'], $attr->getValue());
	}

	public function testSetValueResetsAttributeValue()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::MULTIPLE);

		$attr->setValue('fizz');
		$this->assertSame(['fizz'], $attr->getValue());

		$attr->setValue('buzz');
		$this->assertSame(['buzz'], $attr->getValue());
	}

	public function testMultipleAttributeAllowsStringArray()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::MULTIPLE);

		$attr->setValue(['fizz', 'buzz']);
		$this->assertSame(['fizz', 'buzz'], $attr->getValue());
	}

	/**
	 * @expectedException \Dormilich\WebService\RIPE\InvalidDataTypeException
	 */
	public function testMultipleAttributeDoesNotAllowNonScalarArray()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::MULTIPLE);
		$attr->setValue([NULL]);
	}

	/**
	 * @expectedException \Dormilich\WebService\RIPE\InvalidDataTypeException
	 */
	public function testMultipleAttributeDoesNotAllowNestedArray()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::MULTIPLE);
		$attr->setValue(['bar', [1,2,3]]);
	}

	public function testMultipleAttributeIgnoresArrayKeys()
	{
		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::MULTIPLE);

		$attr->setValue(['fizz' => 'buzz']);
		$this->assertSame(['buzz'], $attr->getValue());
	}

	public function testSingleAttributeConvertsToArray()
	{
		$array = [
			['name' => 'foo', 'value' => 'bar']
		];

		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::SINGLE);
		$attr->setValue('bar');

		$this->assertSame($array, $attr->toArray());
	}

	public function testMultipleAttributeConvertsToArray()
	{
		$array = [
			['name' => 'foo', 'value' => 'bar'],
			['name' => 'foo', 'value' => 'baz'],
		];

		$attr = new Attribute('foo', Attribute::REQUIRED, Attribute::MULTIPLE);
		$attr->addValue('bar');
		$attr->addValue('baz');

		$this->assertSame($array, $attr->toArray());
	}
}
