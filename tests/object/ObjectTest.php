<?php

use Dormilich\WebService\RIPE\AttributeValue;
use Dormilich\WebService\RIPE\Exceptions\IncompleteRPSLObjectException;
use Dormilich\WebService\RIPE\Exceptions\InvalidAttributeException;
use PHPUnit\Framework\TestCase;
use Test\TestObject;

/**
 * ObjectTest
 */
class ObjectTest extends TestCase
{
    // testing the ObjectInterface implementation (1)
    // these tests don’t need an attribute value set

    /**
     * @return void
     */
    public function testObjectInterfaceIsImplemented()
    {
        $obj = new TestObject;
        $this->assertInstanceOf('\Dormilich\WebService\RIPE\ObjectInterface', $obj);
    }

    /**
     * @return void
     */
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
        $this->expectException('LogicException');

        new TestObject(NULL);
    }

    /**
     * @return void
     */
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
        $this->expectException('LogicException');

        new TestObject('foo', NULL);
    }

    /**
     * @return void
     */
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
     * @return void
     */
    public function testGetUnknownAttributeFails()
    {
        $this->expectException(InvalidAttributeException::class);

        $obj = new TestObject;
        $obj->getAttribute('12345');
    }

    /**
     * @return void
     */
    public function testSetSingleAttributeValue()
    {
        $obj = new TestObject;
        $obj->setAttribute('bar', 'buzz');
        $this->assertSame('buzz', $obj->getAttribute('bar')->getValue());
    }

    // testing the ArrayAccess implementation
    // these tests rely on getAttribute()

    /**
     * @return void
     */
    public function testSetAttributeValueAsArray()
    {
        $obj = new TestObject;
        $obj['bar'] = 'buzz';
        $this->assertSame('buzz', $obj->getAttribute('bar')->getValue());
    }

    /**
     * @return void
     */
    public function testGetAttributeValueAsArray()
    {
        $obj = new TestObject;
        $obj->setAttribute('bar', 'buzz');
        $this->assertSame('buzz', $obj['bar']);
    }

    /**
     * @return void
     */
    public function testAttributeCanBeUnset()
    {
        $obj = new TestObject;
        $obj['bar'] = 'buzz';
        unset($obj['bar']);
        $this->assertFalse($obj->getAttribute('bar')->isDefined());
    }

    /**
     * @return void
     */
    public function testAttributeExistence()
    {
        $obj = new TestObject;
        $this->assertTrue(isset($obj['bar']));
        $this->assertFalse(isset($obj['xyz']));
    }

    // testing Countable implementation

    /**
     * @return void
     */
    public function testObjectIsCountable()
    {
        $obj = new TestObject;
        $this->assertSame(1, count($obj));

        $obj['bar'] = 'fizz';
        $this->assertSame(2, count($obj));
    }

    // testing JsonSerialisable implementation

    /**
     * @return void
     */
    public function testObjectIsJsonSerialisable()
    {
        $obj = new TestObject;
        $obj['bar'] = 'foo';
        $obj['choice'] = 'c';
        $this->assertNotFalse(json_encode($obj));
    }

    // testing IteratorAggregate implementation

    /**
     * @return void
     */
    public function testObjectIsTraversable()
    {
        $obj = new TestObject;
        $this->assertInstanceOf('\Traversable', $obj->getIterator());
    }

    // testing the ObjectInterface implementation (2)
    // these tests require set attributes

    /**
     * @return void
     */
    public function testGetPrimaryAttributeValue()
    {
        $obj = new TestObject;
        $obj['bar'] = 'buzz';
        $this->assertSame('buzz', $obj->getPrimaryKey());
    }

    /**
     * @return void
     */
    public function testObjectValidity()
    {
        $obj = new TestObject;
        $this->assertFalse($obj->isValid());
        $obj['bar'] = 'foo';
        $obj['choice'] = 'c';
        $this->assertTrue($obj->isValid());
    }

    /**
     * @return void
     */
    public function testObjectAddAttributeValues()
    {
        $obj = new TestObject;
        $obj->setAttribute('abc', 'x');
        $this->assertEquals(['x'], $obj['abc']);
        $obj->addAttribute('abc', 'y');
        $this->assertEquals(['x', 'y'], $obj['abc']);
    }

    /**
     * @return void
     */
    public function testObjectToArray()
    {
        $bar = new AttributeValue('bar');
        $bar->setComment('testing a value object');

        $obj = new TestObject;
        $obj
            ->addAttribute('bar', $bar)
            ->addAttribute('abc', 'x')
            ->addAttribute('abc', 'y')
            ->addAttribute('abc', 'z')
            ->addAttribute('num', 1)
            ->addAttribute('choice', 'c')
            ->addAttribute('source', 'test');
        $array = $obj->toArray();

        $ref = json_decode(file_get_contents(__DIR__ . '/_fixtures/test.json'), true);
        $this->assertEquals($ref, $array);
    }

    /**
     * @return void
     */
    public function testIncompleteObjectToArrayFails()
    {
        $this->expectException(IncompleteRPSLObjectException::class);

        $obj = new TestObject;
        $this->assertFalse($obj->isValid());
        $obj->toArray();
    }

    /**
     * @return void
     */
    public function testObjectToXML()
    {
        $bar = new AttributeValue('bar');
        $bar->setComment('testing a value object');

        $obj = new TestObject;
        $obj
            ->addAttribute('bar', $bar)
            ->addAttribute('abc', 'x')
            ->addAttribute('abc', 'y')
            ->addAttribute('abc', 'z')
            ->addAttribute('num', 1)
            ->addAttribute('choice', 'c')
            ->addAttribute('source', 'test');
        $xml = $obj->toXML();

        $this->assertSame('test', (string)$xml->objects->object->source['id']);

        $ref = simplexml_load_file(__DIR__ . '/_fixtures/test.xml');
        $this->assertEquals($ref, $xml);
    }

    /**
     * @return void
     */
    public function testIncompleteObjectToXMLFails()
    {
        $this->expectException(IncompleteRPSLObjectException::class);

        $obj = new TestObject;
        $this->assertFalse($obj->isValid());
        $obj->toXML();
    }

    /**
     * @return void
     */
    public function testObjectToString()
    {
        $bar = new AttributeValue('bar');
        $bar->setComment('testing a value object');

        $obj = new TestObject;
        $obj
            ->addAttribute('bar', $bar)
            ->addAttribute('abc', 'x')
            ->addAttribute('abc', 'y')
            ->addAttribute('abc', 'z')
            ->addAttribute('num', 1)
            ->addAttribute('source', 'test');
        $string = trim((string)$obj);
        $lines = explode(\PHP_EOL, $string);

        $this->assertCount(7, $lines);

        $title = array_shift($lines);
        $this->assertNotFalse(strpos($title, 'TestObject'));

        $getData = function ($str) {
            preg_match('/^\s+(\S+)\s+(.+)$/', $str, $match);
            array_shift($match);
            return $match;
        };

        $this->assertEquals(['bar', 'bar # testing a value object'], call_user_func($getData, $lines[0]));
        $this->assertEquals(['abc', 'x'], call_user_func($getData, $lines[1]));
        $this->assertEquals(['abc', 'y'], call_user_func($getData, $lines[2]));
        $this->assertEquals(['abc', 'z'], call_user_func($getData, $lines[3]));
        $this->assertEquals(['num', '1'], call_user_func($getData, $lines[4]));
        $this->assertEquals(['source', 'test'], call_user_func($getData, $lines[5]));
    }

    /**
     * @return void
     */
    public function testGetAttributeNames()
    {
        $obj = new TestObject;

        $attr = $obj->getAttributeNames();
        $this->assertEquals(['bar', 'abc', 'choice', 'num', 'source'], $attr);

        $all = $obj->getAttributeNames(true);
        $this->assertEquals(['bar', 'abc', 'choice', 'num', 'source', 'changed'], $all);
    }
}
