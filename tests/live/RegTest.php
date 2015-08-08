<?php

use Dormilich\WebService\RIPE\RPSL\Person;
use Dormilich\WebService\RIPE\WebService;
use Dormilich\WebService\RIPE\RegWebService;

class RegTest extends PHPUnit_Framework_TestCase
{
	private $ripe;

	public function getRIPE(array $options = array())
	{
		if (NULL === $this->ripe) {
			$this->ripe = new RegWebService( new Test\Guzzle6Adapter($options) );
		}
		return $this->ripe;
	}

	/**
	 * @group live
	 */
	public function testCreatePerson()
	{
		$person = new Person;

		$person['person']  = 'Pauleth Palthen';
		$person['address'] = 'Singel 258';
		$person['phone']   = '+31-1234567890';
		$person['e-mail']  = 'noreply@ripe.net';
		$person['mnt-by']  = 'TEST-DBM-MNT';
		$person['remarks'] = 'created while testing';

		$obj = $this->getRIPE()->create($person);

		$this->assertSame('person', $obj->getType());

		$this->assertSame('Pauleth Palthen',      $obj['person']);
		$this->assertEquals(['Singel 258'],       $obj['address']);
		$this->assertEquals(['+31-1234567890'],   $obj['phone']);
		$this->assertEquals(['noreply@ripe.net'], $obj['e-mail']);
		$this->assertEquals(['TEST-DBM-MNT'],     $obj['mnt-by']);

		$this->assertSame('TEST', $obj['source']);

		return $obj;
	}

	/**
	 * @group live
	 * @depends testCreatePerson
	 */
	public function testUpdatePerson($person)
	{
		$person
			->addAttribute('address', 'Houston, TX')
			->setAttribute('phone',   '+31-0987654321')
			->setAttribute('e-mail',  'ppalse@ripe.net')
		;
		$last_mod = $person['last-modified'];
		// make sure there is a change in the timestamp
		sleep(1);

		$obj = $this->getRIPE()->update($person);

		$this->assertSame('person', $obj->getType());

		$this->assertSame('Pauleth Palthen',     $obj['person']);
		$this->assertEquals(['+31-0987654321'],  $obj['phone']);
		$this->assertEquals(['ppalse@ripe.net'], $obj['e-mail']);
		$this->assertEquals(['TEST-DBM-MNT'],    $obj['mnt-by']);
		$this->assertEquals([
			'Singel 258',
			'Houston, TX',
		], $obj['address']);

		$this->assertNotEquals($last_mod, $obj['last-modified']);

		return $obj;
	}

	/**
	 * @group live
	 * @depends testUpdatePerson
	 */
	public function testReadPerson($person)
	{
		// make sure the object isn’t just routed through
		$lookup = new Person($person->getPrimaryKey());
		$obj    = $this->getRIPE()->read($lookup);

		// emails are stripped off the WHOIS request (privacy)
		unset($person['e-mail']);
		$this->assertEquals($person, $obj);

		return $obj;
	}

	/**
	 * @group live
	 * @depends testReadPerson
	 * @expectedException GuzzleHttp\Exception\ClientException
	 */
	public function testDeletePerson($person)
	{
		$this->getRIPE()->delete($person);
		$this->getRIPE()->read($person);
	}
}
