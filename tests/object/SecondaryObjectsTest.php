<?php

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\RPSL\Person;
use Dormilich\WebService\RIPE\RPSL\PoeticForm;
use Dormilich\WebService\RIPE\RPSL\Role;
use PHPUnit\Framework\TestCase;

/**
 * SecondaryObjectsTest
 */
class SecondaryObjectsTest extends TestCase
{
    /**
     * @return string[][]
     */
    public static function objectTypeProvider()
    {
        return [
            ['AsBlock', 'as-block'],
            ['Irt', 'irt'],
            ['KeyCert', 'key-cert'],
            ['Mntner', 'mntner'],
            ['Organisation', 'organisation'],
            ['Poem', 'poem'],
        ];
    }

    /**
     * @dataProvider objectTypeProvider
     */
    public function testObjectTypeAndKey($class, $type)
    {
        $class = 'Dormilich\\WebService\\RIPE\\RPSL\\' . $class;
        $obj = new $class('123');
        $this->assertEquals($type, $obj->getType());
        $this->assertEquals('123', $obj->getPrimaryKey());
    }

    /**
     * @return void
     */
    public function testPersonTypeAndKey()
    {
        $obj = new Person('123');
        $this->assertEquals('person', $obj->getType());
        $this->assertEquals('123', $obj->getPrimaryKey());
    }

    /**
     * @return void
     */
    public function testPoeticformTypeAndKey()
    {
        $obj = new PoeticForm('FORM-HAIKU');
        $this->assertEquals('poetic-form', $obj->getType());
        $this->assertEquals('FORM-HAIKU', $obj->getPrimaryKey());
    }

    /**
     * @return void
     */
    public function testRoleTypeAndKey()
    {
        $obj = new Role('123');
        $this->assertEquals('role', $obj->getType());
        $this->assertEquals('123', $obj->getPrimaryKey());
    }
}
