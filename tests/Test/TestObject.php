<?php

namespace Test;

use Dormilich\WebService\RIPE\Object;
use Dormilich\WebService\RIPE\AttributeInterface as A;

class TestObject extends Object
{
	public function __construct()
	{
		$this->setType('foo');
		$this->setKey('bar');
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
