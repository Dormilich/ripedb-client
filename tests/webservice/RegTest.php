<?php

use Dormilich\WebService\RIPE\RPSL\Person;
use Dormilich\WebService\RIPE\RPSL\Inetnum;
use Dormilich\WebService\RIPE\WebService;
use Dormilich\WebService\RIPE\RegWebService;
use Test\RegObject;

class RegTest extends PHPUnit_Framework_TestCase
{
	public function load($name)
	{
		if (!$name) {
			return [];
		}

		$file = __DIR__ . '/_fixtures/' . $name . '.json';

		if (!is_readable($file)) {
			throw new RuntimeException("File $name.json not found.");
		}

		return json_decode(file_get_contents($file), true);
	}

	public function getClient($name = NULL)
	{
		return new Test\MockClient($this->load($name));
	}

	// create

	public function testClientGetsCorrectCreateRequest()
	{
		$client = $this->getClient();
		$ripe   = new RegWebService($client);
		$obj    = new RegObject('create');

		$ripe->create($obj);

		$expected = '{"objects":{"object":{"source":{"id":"TEST"},"attributes":{"attribute":[{"name":"register","value":"create"},{"name":"source","value":"TEST"}]}}}}';

		$this->assertEquals('POST', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/test/register?password=emptypassword', $client->path);
		$this->assertEquals($expected, $client->body);
	}

	// update

	public function testClientGetsCorrectUpdateRequest()
	{
		$client = $this->getClient();
		$ripe   = new RegWebService($client);
		$obj    = new RegObject('update');

		$ripe->update($obj);

		$expected = '{"objects":{"object":{"source":{"id":"TEST"},"attributes":{"attribute":[{"name":"register","value":"update"},{"name":"source","value":"TEST"}]}}}}';

		$this->assertEquals('PUT', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/test/register/update?password=emptypassword', $client->path);
		$this->assertEquals($expected, $client->body);
	}

	// delete

	public function testClientGetsCorrectDeleteRequest()
	{
		$client = $this->getClient();
		$ripe   = new RegWebService($client);

		$person = new Person('FOO');
		$ripe->delete($person);

		$this->assertEquals('DELETE', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/test/person/FOO?password=emptypassword', $client->path);
		$this->assertNull($client->body);
	}
}