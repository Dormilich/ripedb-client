<?php

use Dormilich\WebService\RIPE\Attribute;
use Dormilich\WebService\RIPE\Exceptions\InvalidValueException;
use Dormilich\WebService\RIPE\MatchedAttribute;
use Dormilich\WebService\RIPE\AttributeInterface as Attr;
use PHPUnit\Framework\TestCase;

/**
 * MatchedAttributeTest
 */
class MatchedAttributeTest extends TestCase
{
    /**
     * @return void
     */
    public function testAttributeInterfaceIsImplemented()
    {
        $attr = new MatchedAttribute('foo', true, '/x/');
        $this->assertInstanceOf(\Dormilich\WebService\RIPE\AttributeInterface::class, $attr);
    }

    /**
     * @return void
     */
    public function testAttributeClassIsExtended()
    {
        $attr = new MatchedAttribute('foo', true, '/x/');
        $this->assertInstanceOf(Attribute::class, $attr);
    }

    /**
     * @return void
     */
    public function testAttributeIsSingle()
    {
        $attr = new MatchedAttribute('foo', true, '/x/');
        $this->assertFalse($attr->isMultiple());
    }

    /**
     * @return void
     */
    public function testAttributeRequiredness()
    {
        $attr = new MatchedAttribute('foo', Attr::REQUIRED, '/x/');
        $this->assertTrue($attr->isRequired());

        $attr = new MatchedAttribute('foo', Attr::OPTIONAL, '/x/');
        $this->assertFalse($attr->isRequired());
    }

    /**
     * @return void
     */
    public function testGetRegexp()
    {
        $attr = new MatchedAttribute('foo', Attr::REQUIRED, '/\bFizzBuzz\b/');
        $this->assertSame('/\bFizzBuzz\b/', $attr->getRegExp());
    }

    /**
     * @return void
     */
    public function testAttributeAcceptsMatchingValue()
    {
        $attr = new MatchedAttribute('foo', Attr::REQUIRED, '/\bFizzBuzz\b/');

        $attr->setValue('Hold onto the FizzBuzz!');
        $this->assertSame('Hold onto the FizzBuzz!', $attr->getValue());
    }

    /**
     * @return void
     */
    public function testAttributeDoesNotAcceptUndefinedValue()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[bar]');

        $attr = new MatchedAttribute('bar', Attr::REQUIRED, '/\bFizzBuzz\b/');
        $attr->setValue('get the fizz buzz');
    }

    /**
     * @return void
     */
    public function testInvalidRegexpThrowsException()
    {
        $this->expectException('LogicException');

        $attr = new MatchedAttribute('bar', Attr::REQUIRED, 'string');
    }
}
