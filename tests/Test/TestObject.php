<?php

namespace Test;

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\AttributeInterface as A;

/**
 * TestObject
 */
class TestObject extends AbstractObject
{
    /**
     * @param $type
     * @param $key
     */
    public function __construct($type = 'foo', $key = 'bar')
    {
        $this->setType($type);
        $this->setKey($key);
        $this->init();
        $this->setAttribute('source', 'test');
    }

    /**
     * @return void
     */
    protected function init()
    {
        $this->create('bar', A::REQUIRED, A::SINGLE);
        $this->create('abc', A::OPTIONAL, A::MULTIPLE);
        $this->fixed('choice', A::REQUIRED, ['a', 'b', 'c']);
        $this->matched('num', A::OPTIONAL, '/\d+/');
        $this->generated('changed');
        // required for serialisation
        $this->create('source', A::REQUIRED, A::SINGLE);
    }
}
