<?php

namespace Test;

use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\AttributeInterface as A;

/**
 * RegObject
 */
class RegObject extends AbstractObject
{
    /**
     * @param $value
     */
    public function __construct($value = 'auto')
    {
        $this->setType('register');
        $this->setKey('register');
        $this->init();
        $this->setAttribute('register', $value);
    }

    /**
     * @return void
     */
    protected function init()
    {
        $this->create('register', A::REQUIRED, A::SINGLE);
        $this->create('comment', A::OPTIONAL, A::MULTIPLE);
        $this->create('source', A::REQUIRED, A::SINGLE);
    }
}
