<?php

use Dormilich\WebService\RIPE\RPSL\Person;
use Dormilich\WebService\RIPE\RPSL\Inetnum;
use Dormilich\WebService\RIPE\WebService;
use Dormilich\WebService\RIPE\WhoisWebService;

class WhoisTest extends PHPUnit_Framework_TestCase
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

	// read

	public function testClientGetsCorrectDefaultRequestParameters()
	{
		$client = $this->getClient();
		$ripe   = new WhoisWebService($client);

		$this->assertSame('sandbox', $ripe->getEnvironment());
		$this->assertFalse($ripe->isProduction());
		$this->assertTrue($ripe->isSSL());

		$person = new Person('FOO-TEST');
		$ripe->read($person);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/test/person/FOO-TEST?unfiltered', $client->path);
		$this->assertNull($client->body);
	}

	public function testClientGetsCorrectCustomRequestParameters()
	{
		$client = $this->getClient();
		$ripe   = new WhoisWebService($client, [
			'ssl'         => false,
			'environment' => WebService::PRODUCTION,
		]);

		$this->assertSame('production', $ripe->getEnvironment());
		$this->assertTrue($ripe->isProduction());
		$this->assertFalse($ripe->isSSL());

		$person = new Person('FOO-TEST');
		$ripe->read($person);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('http://rest.db.ripe.net', $client->uri);
		$this->assertEquals('/ripe/person/FOO-TEST?unfiltered', $client->path);
		$this->assertNull($client->body);
	}

	public function testGetReadResultObject()
	{
		$client = $this->getClient('person');
		$ripe   = new WhoisWebService($client);

		$person = new Person('FOO-TEST');
		$person = $ripe->read($person);

		$this->assertInstanceOf('Dormilich\\WebService\\RIPE\\RPSL\\Person', $person);

		$this->assertCount(1, $ripe->getAllResults());

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

	public function testUndefinedObjectResponseUsesDummyObject()
	{
		$client = $this->getClient('test');
		$ripe   = new WhoisWebService($client);

		$person = new Person('FOO-TEST');
		$object = $ripe->read($person);

		$this->assertInstanceOf('Dormilich\\WebService\\RIPE\\Dummy', $object);
		$this->assertEquals('register', $object->getType());
		$this->assertEquals('ripe', $object->getPrimaryKey());
	}

	// version

	public function testClientGetsCorrectVersionRequest()
	{
		$client = $this->getClient();
		$ripe   = new WhoisWebService($client);

		$ip = new Inetnum('127.0.0.1');
		$ripe->version($ip, 5);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/test/inetnum/127.0.0.1/versions/5?unfiltered', $client->path);
		$this->assertNull($client->body);
	}

	// versions

	public function testClientGetsCorrectVersionsRequest()
	{
		$client = $this->getClient();
		$ripe   = new WhoisWebService($client);

		$ip = new Inetnum('127.0.0.1');
		$ripe->versions($ip);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/test/inetnum/127.0.0.1/versions', $client->path);
		$this->assertNull($client->body);
	}

	public function testGetCorrectVersionsInfo()
	{
		$client = $this->getClient('versions');
		$ripe   = new WhoisWebService($client);

		$ip = new Inetnum('127.0.0.0 - 127.0.0.127');
		$versions = $ripe->versions($ip);

		$expected = [
			'1' => '1970-01-04 09:48 (ADD/UPD)',
			'2' => '1971-04-01 19:44 (ADD/UPD)',
		];
		$this->assertEquals($expected, $versions);
	}

	// search

	public function testClientGetsCorrectSearchRequest()
	{
		$client = $this->getClient();
		$ripe   = new WhoisWebService($client);

		$ripe->search('FOO', [
			'type-filter' 		=> 'role', 
			'inverse-attribute' => ['tech-c', 'admin-c'], 
		]);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/search?type-filter=role&inverse-attribute=tech-c'.
			'&inverse-attribute=admin-c&source=test&query-string=FOO', $client->path);
		$this->assertNull($client->body);
	}

	// abuse

	public function testClientGetsCorrectAbuseRequest()
	{
		$client = $this->getClient();
		$ripe   = new WhoisWebService($client);

		$ip = new Inetnum('127.0.0.1');
		$ripe->abuseContact($ip);

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/abuse-contact/127.0.0.1', $client->path);
		$this->assertNull($client->body);
	}

	public function testGetCorrectAbuseInfo()
	{
		$client = $this->getClient('abuse');
		$ripe   = new WhoisWebService($client);

		$ip = new Inetnum('127.0.0.0 - 127.0.0.127');
		$email = $ripe->abuseContact($ip);

		$this->assertEquals('abuse@example.com', $email);
	}

	// template

	public function testClientGetsCorrectTemplateRequest()
	{
		$client = $this->getClient();
		$ripe   = new WhoisWebService($client);

		$poem = $ripe->getObjectFromTemplate('poem');

		$this->assertEquals('GET', $client->method);
		$this->assertEquals('https://rest-test.db.ripe.net', $client->uri);
		$this->assertEquals('/metadata/templates/poem', $client->path);
		$this->assertNull($client->body);
	}

	public function testGetCorrectTemplateInfo()
	{
		$client = $this->getClient('template');
		$ripe   = new WhoisWebService($client);

		$poem = $ripe->getObjectFromTemplate('poem');

		$this->assertEquals('poem', $poem->getType());
		$this->assertEquals('poem', $poem->getPrimaryKeyName());

		$this->assertTrue($poem->getAttribute('poem')->isRequired());
		$this->assertFalse($poem->getAttribute('poem')->isMultiple());

		$this->assertFalse($poem->getAttribute('descr')->isRequired());
		$this->assertTrue($poem->getAttribute('descr')->isMultiple());

		$this->assertTrue($poem->getAttribute('form')->isRequired());
		$this->assertFalse($poem->getAttribute('form')->isMultiple());

		$this->assertTrue($poem->getAttribute('text')->isRequired());
		$this->assertTrue($poem->getAttribute('text')->isMultiple());

		$this->assertFalse($poem->getAttribute('author')->isRequired());
		$this->assertTrue($poem->getAttribute('author')->isMultiple());

		$this->assertFalse($poem->getAttribute('remarks')->isRequired());
		$this->assertTrue($poem->getAttribute('remarks')->isMultiple());

		$this->assertFalse($poem->getAttribute('notify')->isRequired());
		$this->assertTrue($poem->getAttribute('notify')->isMultiple());

		$this->assertTrue($poem->getAttribute('mnt-by')->isRequired());
		$this->assertFalse($poem->getAttribute('mnt-by')->isMultiple());

		$this->assertFalse($poem->getAttribute('changed')->isRequired());
		$this->assertTrue($poem->getAttribute('changed')->isMultiple());

		$this->assertFalse($poem->getAttribute('created')->isRequired());
		$this->assertFalse($poem->getAttribute('created')->isMultiple());

		$this->assertFalse($poem->getAttribute('last-modified')->isRequired());
		$this->assertFalse($poem->getAttribute('last-modified')->isMultiple());

		$this->assertTrue($poem->getAttribute('source')->isRequired());
		$this->assertFalse($poem->getAttribute('source')->isMultiple());
		$this->assertEquals('ripe', $poem['source']);
	}

	public function testReadErrorsFromInvalidRequest()
	{
		$res  = file_get_contents(__DIR__  . '/_fixtures/error-response.json');
		$list = Webservice::getErrors($res);

		$this->assertCount(5, $list);
		$this->assertEquals("Error: Authorisation for [person] PP1-TEST failed\nusing \"mnt-by:\"\nno valid maintainer found\n", $list[0]);
		$this->assertEquals("Error: The maintainer 'OWNER-MNT' was not found in the database", $list[1]);
		$this->assertEquals("Error: Unknown object referenced OWNER-MNT (mnt-by)", $list[2]);
		$this->assertEquals("Warning: Deprecated attribute \"changed\". This attribute will be removed in the future.", $list[3]);
		$this->assertEquals("Info: To create the first person/mntner pair of objects for an organisation see https://apps.db.ripe.net/startup/", $list[4]);
	}

	public function testReadErrorReturnsEmptyArrayOnInvalidErrorBody()
	{
		$list = Webservice::getErrors('foo');

		$this->assertCount(0, $list);
	}
}
