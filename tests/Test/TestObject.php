<?php

namespace Test;

use Dormilich\WebService\RIPE\RipeObject;
use Dormilich\WebService\RIPE\AttributeInterface as A;

class TestObject extends RipeObject
{
	public function __construct($type = 'foo', $key = 'bar')
	{
		$this->setType($type);
		$this->setKey($key);
		$this->init();
		$this->setAttribute('source', 'test');
	}

	protected function init()
	{
		$this->create('bar',   A::REQUIRED, A::SINGLE);
		$this->create('abc',   A::OPTIONAL, A::MULTIPLE);
		$this->fixed('choice', A::REQUIRED, ['a', 'b', 'c']);
		$this->matched('num',  A::OPTIONAL, '/\d+/');
		$this->generated('changed');
		// required for serialisation
		$this->create('source', A::REQUIRED, A::SINGLE);
	}
}
