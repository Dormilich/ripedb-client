<?php

use Dormilich\WebService\RIPE\RPSL\Person;
use Dormilich\WebService\RIPE\WebService;
use Dormilich\WebService\RIPE\WhoisWebService;

class WhoisTest extends PHPUnit_Framework_TestCase
{
	public function load($name)
	{
		$file = __DIR__ . '/_fixtures/' . $name . '.json';

		if (!is_readable($file)) {
			throw new RuntimeException("File $name.json not found.");
		}

		return json_decode(file_get_contents($file), true);
	}

	public function getClient($name)
	{
		return new Test\MockClient($this->load($name));
	}

	public function testClientGetsCorrectDefaultRequestParameters()
	{
		$client = new Test\MockClient([]);
		$ripe   = new WhoisWebService($client);

		$person = new Person('FOO-TEST');
		$ripe->read($person);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/TEST/person/FOO-TEST?unfiltered', $client->path);
		$this->assertNull($client->body);
	}

	public function testClientGetsCorrectCustomRequestParameters()
	{
		$client = new Test\MockClient([]);
		$ripe   = new WhoisWebService($client, [
			'ssl'         => false,
			'environment' => WebService::PRODUCTION,
		]);

		$person = new Person('FOO-TEST');
		$ripe->read($person);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('http://rest.db.ripe.net', $client->uri);
		$this->assertEquals('/RIPE/person/FOO-TEST?unfiltered', $client->path);
		$this->assertNull($client->body);
	}

	public function testClientGetsCorrectVersionRequest()
	{
		$client = new Test\MockClient([]);
		$ripe   = new WhoisWebService($client);

		$person = new Person('FOO-TEST');
		$ripe->version($person, 5);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/TEST/person/FOO-TEST/versions/5?unfiltered', $client->path);
		$this->assertNull($client->body);
	}

	// test other parameters

	public function testReadSingleObject()
	{
		$client = $this->getClient('person');
		$ripe   = new WhoisWebService($client);

		$person = new Person('FOO-TEST');
		$person = $ripe->read($person);

		$this->assertInstanceOf('Dormilich\\WebService\\RIPE\\RPSL\\Person', $person);

		$this->assertEquals('FOO-TEST', $person->getPrimaryKey());
		$this->assertEquals('John Smith', $person['person']);
		$this->assertEquals([
			"Example, Ltd.", 
			"Road to Mandalay 1", 
			"1234 Gareth", 
			"Aventuria", 
		], $person['address']);
		$this->assertEquals(["+0 1234 123456"], $person['phone']);
		$this->assertEquals(["+0 1234 123457"], $person['fax-no']);
		$this->assertEquals(["john.smith@example.com"], $person['e-mail']);
		$this->assertEquals("FOO-TEST", $person['nic-hdl']);
		$this->assertEquals(["FOO-MNT"], $person['mnt-by']);
		$this->assertEquals("1970-01-01T00:00:00Z", $person['created']);
		$this->assertEquals("1970-01-01T00:00:00Z", $person['last-modified']);
		$this->assertEquals("RIPE", $person['source']);
	}
}
