<?php

use Dormilich\WebService\RIPE\Exceptions\InvalidValueException;
use Dormilich\WebService\RIPE\RPSL\Person;
use Dormilich\WebService\RIPE\RPSL\Inetnum;
use Dormilich\WebService\RIPE\WebService;
use PHPUnit\Framework\TestCase;
use Test\MockClient;
use Test\RegObject;

class QueryTest extends TestCase
{
	public function getClient(): MockClient
    {
		return new MockClient('[]');
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
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
	}

	public function testClientGetsCorrectUrlForNoOptions()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);

		$person = new Person('FOO-TEST');
		$ripe->read($person, []);

        $this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/person/FOO-TEST', $client->url);
        $this->assertNull($client->body);
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
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

        $this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest.db.ripe.net/ripe/person/FOO-TEST', $client->url);
        $this->assertNull($client->body);
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
	}

	public function testClientGetsCorrectCustomRequestParameters()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client, [
			'environment' => WebService::PRODUCTION,
		]);
        $ripe->setUsername(NULL);

		$this->assertSame('production', $ripe->getEnvironment());
		$this->assertTrue($ripe->isProduction());

		$person = new Person('FOO-TEST');
		$ripe->read($person);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest.db.ripe.net/ripe/person/FOO-TEST?unfiltered', $client->url);
		$this->assertNull($client->body);
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
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

        $this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/person/FOO-TEST', $client->url);
        $this->assertNull($client->body);
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
	}

	// version

	public function testClientGetsCorrectVersionRequest()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);
        $ripe->setUsername(NULL);

		$ip = new Inetnum('127.0.0.1');
		$ripe->version($ip, 5);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/inetnum/127.0.0.1/versions/5?unfiltered', $client->url);
		$this->assertNull($client->body);
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
	}

	// versions

	public function testClientGetsCorrectVersionsRequest()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);
        $ripe->setUsername(NULL);

		$ip = new Inetnum('127.0.0.1');
		$ripe->versions($ip);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/test/inetnum/127.0.0.1/versions', $client->url);
		$this->assertNull($client->body);
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
	}

	// search

	public function testClientGetsCorrectSearchRequestFromArray()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);
        $ripe->setUsername(NULL);

		$ripe->search('FOO', [
			'type-filter' 		=> 'role',
			'inverse-attribute' => ['tech-c', 'admin-c'],
		]);

		$url = 'https://rest-test.db.ripe.net/search?type-filter=role&inverse-attribute=tech-c'
			 . '&inverse-attribute=admin-c&source=test&query-string=FOO';

		$this->assertEquals('GET', $client->method);
		$this->assertEquals($url, $client->url);
		$this->assertNull($client->body);
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
	}

	public function testClientGetsCorrectSearchRequestFromString()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);
        $ripe->setUsername(NULL);

		$ripe->search('FOO', 'type-filter=role&inverse-attribute=tech-c&inverse-attribute=admin-c');

		$url = 'https://rest-test.db.ripe.net/search?type-filter=role&inverse-attribute=tech-c'
			 . '&inverse-attribute=admin-c&source=test&query-string=FOO';

		$this->assertEquals('GET', $client->method);
		$this->assertEquals($url, $client->url);
		$this->assertNull($client->body);
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
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
        $ripe->setUsername(NULL);

		$ip = new Inetnum('127.0.0.1');
		$ripe->abuseContact($ip);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/abuse-contact/127.0.0.1', $client->url);
		$this->assertNull($client->body);
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
	}

	// template

	public function testClientGetsCorrectTemplateRequest()
	{
		$client = $this->getClient();
		$ripe   = new WebService($client);
        $ripe->setUsername(NULL);

		$ripe->getObjectFromTemplate('poem');

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net/metadata/templates/poem', $client->url);
		$this->assertNull($client->body);
        $this->assertArrayNotHasKey('Authorization', $client->header);
        $this->assertArrayHasKey('Accept', $client->header);
        $this->assertEquals('application/json', $client->header['Accept']);
	}
}
