<?php

namespace Test;

/**
 * StringObject
 */
class StringObject
{
    /**
     * @return string
     */
    public function __toString()
	{
		return 'test';
	}
}
