<?php

use Dormilich\WebService\RIPE\Attribute;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;
use Dormilich\WebService\RIPE\Exceptions\InvalidDataTypeException;
use PHPUnit\Framework\TestCase;

/**
 * AttributeTest
 */
class AttributeTest extends TestCase
{
    /**
     * @return void
     */
    public function testAttributeInterfaceIsImplemented()
    {
        $attr = new Attribute('foo', true, true);
        $this->assertInstanceOf(\Dormilich\WebService\RIPE\AttributeInterface::class, $attr);
    }

    /**
     * @return void
     */
    public function testAttributeHasCorrectName()
    {
        $attr = new Attribute('foo', true, true);
        $this->assertSame('foo', $attr->getName());

        $attr = new Attribute(1.8, true, true);
        $this->assertSame('1.8', $attr->getName());
    }

    /**
     * @return void
     */
    public function testAttributeIsEmptyByDefault()
    {
        $attr = new Attribute('foo', true, true);
        $this->assertFalse($attr->isDefined());
        $this->assertNull($attr->getValue());
    }

    /**
     * @return array
     */
    public static function constructorPropertyProvider()
    {
        return [
            [true, true, true, true], [true, false, true, false],
            [false, true, false, true], [false, false, false, false],
            [0, 1, false, true], ['x', NULL, true, false],
            [Attr::REQUIRED, Attr::SINGLE, true, false],
            [Attr::OPTIONAL, Attr::MULTIPLE, false, true],
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

    /**
     * @return void
     */
    public function testAttributeConvertsInputToStrings()
    {
        $attr = new Attribute('foo', Attr::REQUIRED, Attr::SINGLE);

        $attr->setValue(1);
        $this->assertSame('1', $attr->getValue());

        $attr->setValue(2.718);
        $this->assertSame('2.718', $attr->getValue());

        $attr->setValue('bar');
        $this->assertSame('bar', $attr->getValue());

        $test = new Test\StringObject;
        $attr->setValue($test);
        $this->assertSame('test', $attr->getValue());

        // I am not aware that the RIPE DB uses booleans somewhere…
        $attr->setValue(true);
        $this->assertSame('true', $attr->getValue());

        $attr->setValue(false);
        $this->assertSame('false', $attr->getValue());
    }

    /**
     * @return void
     */
    public function testNullResetsAttributeValue()
    {
        $attr = new Attribute('foo', Attr::REQUIRED, Attr::SINGLE);
        $attr->setValue('foo');
        $attr->setValue(NULL);
        $this->assertFalse($attr->isDefined());
    }

    /**
     * @return void
     */
    public function testAttributeDoesNotAcceptResource()
    {
        $this->expectException(InvalidDataTypeException::class);
        $this->expectExceptionMessage('[foo]');

        $attr = new Attribute('foo', Attr::REQUIRED, Attr::SINGLE);
        $attr->setValue(tmpfile());
    }

    /**
     * @return void
     */
    public function testAttributeDoesNotAcceptObject()
    {
        $this->expectException(InvalidDataTypeException::class);
        $this->expectExceptionMessage('[foo]');

        $attr = new Attribute('foo', Attr::REQUIRED, Attr::SINGLE);
        $attr->setValue(new stdClass);
    }

    /**
     * @return void
     */
    public function testSingleAttributeOnlyHasOneValue()
    {
        $attr = new Attribute('foo', Attr::REQUIRED, Attr::SINGLE);

        $attr->setValue('fizz');
        $this->assertSame('fizz', $attr->getValue());

        $attr->setValue('buzz');
        $this->assertSame('buzz', $attr->getValue());

        $attr->addValue('bar');
        $this->assertSame('bar', $attr->getValue());
    }

    /**
     * @return void
     */
    public function testSingleAttributeDoesNotAllowArrayInput()
    {
        $this->expectException(InvalidDataTypeException::class);

        $attr = new Attribute('foo', Attr::REQUIRED, Attr::SINGLE);
        $attr->setValue(['fizz', 'buzz']);
    }

    /**
     * @return void
     */
    public function testMultipleAttributeReturnsList()
    {
        $attr = new Attribute('foo', Attr::REQUIRED, Attr::MULTIPLE);

        $attr->addValue('fizz');
        $this->assertSame(['fizz'], $attr->getValue());

        $attr->addValue('buzz');
        $this->assertSame(['fizz', 'buzz'], $attr->getValue());
    }

    /**
     * @return void
     */
    public function testSetValueResetsAttributeValue()
    {
        $attr = new Attribute('foo', Attr::REQUIRED, Attr::MULTIPLE);

        $attr->setValue('fizz');
        $this->assertSame(['fizz'], $attr->getValue());

        $attr->setValue('buzz');
        $this->assertSame(['buzz'], $attr->getValue());
    }

    /**
     * @return void
     */
    public function testMultipleAttributeAllowsStringArray()
    {
        $attr = new Attribute('foo', Attr::REQUIRED, Attr::MULTIPLE);

        $attr->setValue(['fizz', 'buzz']);
        $this->assertSame(['fizz', 'buzz'], $attr->getValue());
    }

    /**
     * @return void
     */
    public function testMultipleAttributeDoesNotAllowNonScalarArray()
    {
        $this->expectException(InvalidDataTypeException::class);

        $attr = new Attribute('foo', Attr::REQUIRED, Attr::MULTIPLE);
        $attr->setValue([NULL]);
    }

    /**
     * @return void
     */
    public function testMultipleAttributeDoesNotAllowNestedArray()
    {
        $this->expectException(InvalidDataTypeException::class);

        $attr = new Attribute('foo', Attr::REQUIRED, Attr::MULTIPLE);
        $attr->setValue(['bar', [1, 2, 3]]);
    }

    /**
     * @return void
     */
    public function testMultipleAttributeIgnoresArrayKeys()
    {
        $attr = new Attribute('foo', Attr::REQUIRED, Attr::MULTIPLE);

        $attr->setValue(['fizz' => 'buzz']);
        $this->assertSame(['buzz'], $attr->getValue());
    }
}
