<?php

namespace Test;

use Dormilich\WebService\RIPE\RipeObject;
use Dormilich\WebService\RIPE\AttributeInterface as A;

class RegObject extends RipeObject
{
	public function __construct($value = 'auto')
	{
		$this->setType('register');
		$this->setKey('register');
		$this->init();
		$this->setAttribute('register', $value);
	}

	protected function init()
	{
		$this->create('register', A::REQUIRED, A::SINGLE);
		$this->create('comment',  A::OPTIONAL, A::MULTIPLE);
		$this->create('source',   A::REQUIRED, A::SINGLE);
	}
}
