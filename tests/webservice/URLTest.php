<?php

use Dormilich\WebService\RIPE\Exceptions\InvalidValueException;
use Dormilich\WebService\RIPE\RPSL\Person;
use Dormilich\WebService\RIPE\RPSL\Inetnum;
use Dormilich\WebService\RIPE\WebService;
use PHPUnit\Framework\TestCase;
use Test\RegObject;

class URLTest extends TestCase
{
	public function getClient($name = NULL)
	{
		return new Test\MockClient('[]');
	}

	// read

	public function testClientGetsCorrectDefaultRequestParameters()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$this->assertSame('sandbox', $ripe->getEnvironment());
		$this->assertFalse($ripe->isProduction());

		$person = new Person('FOO-TEST');
		$ripe->read($person);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/person/FOO-TEST?unfiltered', $client->url);
		$this->assertNull($client->body);
	}

	public function testClientGetsCorrectUrlForNoOptions()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$person = new Person('FOO-TEST');
		$ripe->read($person, []);

		$this->assertEquals('https://rest-test.db.ripe.net/test/person/FOO-TEST', $client->url);
	}

	public function testClientGetsCorrectProductionUrlAfterChange()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);
		$this->assertFalse($ripe->isProduction());

		$ripe->setEnvironment(WebService::PRODUCTION);
		$this->assertTrue($ripe->isProduction());

		$person = new Person('FOO-TEST');
		$ripe->read($person, []);

		$this->assertEquals('https://rest.db.ripe.net/ripe/person/FOO-TEST', $client->url);
	}

	public function testClientGetsCorrectCustomRequestParameters()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client, [
			'environment' => WebService::PRODUCTION,
		]);

		$this->assertSame('production', $ripe->getEnvironment());
		$this->assertTrue($ripe->isProduction());

		$person = new Person('FOO-TEST');
		$ripe->read($person);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest.db.ripe.net/ripe/person/FOO-TEST?unfiltered', $client->url);
		$this->assertNull($client->body);
	}

	public function testClientGetsCorrectSandboxUrlAfterChange()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client, [
			'environment' => WebService::PRODUCTION,
		]);
		$this->assertTrue($ripe->isProduction());

		$ripe->setEnvironment(WebService::SANDBOX);
		$this->assertFalse($ripe->isProduction());

		$person = new Person('FOO-TEST');
		$ripe->read($person, []);

		$this->assertEquals('https://rest-test.db.ripe.net/test/person/FOO-TEST', $client->url);
	}

	// version

	public function testClientGetsCorrectVersionRequest()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$ip = new Inetnum('127.0.0.1');
		$ripe->version($ip, 5);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/inetnum/127.0.0.1/versions/5?unfiltered', $client->url);
		$this->assertNull($client->body);
	}

	// versions

	public function testClientGetsCorrectVersionsRequest()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$ip = new Inetnum('127.0.0.1');
		$ripe->versions($ip);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/inetnum/127.0.0.1/versions', $client->url);
		$this->assertNull($client->body);
	}

	// search

	public function testClientGetsCorrectSearchRequestFromArray()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$ripe->search('FOO', [
			'type-filter' 		=> 'role', 
			'inverse-attribute' => ['tech-c', 'admin-c'], 
		]);

		$url = 'https://rest-test.db.ripe.net/search?type-filter=role&inverse-attribute=tech-c'
			 . '&inverse-attribute=admin-c&source=test&query-string=FOO';

		$this->assertEquals('GET', $client->method);
		$this->assertEquals($url, $client->url);
		$this->assertNull($client->body);
	}

	public function testClientGetsCorrectSearchRequestFromString()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$ripe->search('FOO', 'type-filter=role&inverse-attribute=tech-c&inverse-attribute=admin-c');

		$url = 'https://rest-test.db.ripe.net/search?type-filter=role&inverse-attribute=tech-c'
			 . '&inverse-attribute=admin-c&source=test&query-string=FOO';

		$this->assertEquals('GET', $client->method);
		$this->assertEquals($url, $client->url);
		$this->assertNull($client->body);
	}

	public function testSearchRequestFailsOnNonQuery()
	{
	    $this->expectException(InvalidValueException::class);

		$client = $this->getClient();
		$ripe   = new WebService($client);

		$ripe->search('FOO', new stdClass);
	}

	// abuse

	public function testClientGetsCorrectAbuseRequest()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$ip = new Inetnum('127.0.0.1');
		$ripe->abuseContact($ip);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/abuse-contact/127.0.0.1', $client->url);
		$this->assertNull($client->body);
	}

	// template

	public function testClientGetsCorrectTemplateRequest()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$poem = $ripe->getObjectFromTemplate('poem');

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/metadata/templates/poem', $client->url);
		$this->assertNull($client->body);
	}

	// create

	public function testClientGetsCorrectCreateRequest()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);
		$obj    = new RegObject('create');

		$ripe->create($obj);

		$expected = '{"objects":{"object":[{"source":{"id":"TEST"},"attributes":{"attribute":[{"name":"register","value":"create"},{"name":"source","value":"TEST"}]}}]}}';

		$this->assertEquals('POST', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/register?password=emptypassword', $client->url);
		$this->assertEquals($expected, $client->body);
	}

	public function testClientGetsCorrectCreateUrlAfterChange()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);
		$obj    = new RegObject('create');

		$ripe->setEnvironment(WebService::PRODUCTION);
		$ripe->setPassword('super-secret');

		$ripe->create($obj);

		$this->assertEquals('https://rest.db.ripe.net/ripe/register?password=super-secret', $client->url);
	}

	// update

	public function testClientGetsCorrectUpdateRequest()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);
		$obj    = new RegObject('update');

		$ripe->update($obj);

		$expected = '{"objects":{"object":[{"source":{"id":"TEST"},"attributes":{"attribute":[{"name":"register","value":"update"},{"name":"source","value":"TEST"}]}}]}}';

		$this->assertEquals('PUT', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/register/update?password=emptypassword', $client->url);
		$this->assertEquals($expected, $client->body);
	}

	// delete

	public function testClientGetsCorrectDeleteRequest()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$person = new RegObject('FOO');
		$ripe->delete($person);

		$this->assertEquals('DELETE', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/register/FOO?password=emptypassword', $client->url);
		$this->assertNull($client->body);
	}

	public function testClientGetsCorrectDeleteRequestWithReason()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$person = new RegObject('FOO');
		$ripe->delete($person, 'because I can!');

		$this->assertEquals('DELETE', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/register/FOO?reason=because%20I%20can%21&password=emptypassword', $client->url);
		$this->assertNull($client->body);
	}
}
