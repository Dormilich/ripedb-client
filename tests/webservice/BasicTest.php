<?php

use PHPUnit\Framework\TestCase;

use Dormilich\WebService\RIPE\WebService;
use Test\MockClient;
use Test\RegObject;

class BasicTest extends TestCase
{
    public function getClient(): MockClient
    {
        return new MockClient('[]');
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
        $this->assertEquals('https://rest-test.db.ripe.net/test/register', $client->url);
        $this->assertEquals($expected, $client->body);
        $this->assertArrayHasKey('Authorization', $client->header);
        $this->assertEquals('Basic VEVTVC1EQk0tTU5UOmVtcHR5cGFzc3dvcmQ=', $client->header['Authorization']);
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
        $this->assertEquals('https://rest-test.db.ripe.net/test/register/update', $client->url);
        $this->assertEquals($expected, $client->body);
        $this->assertArrayHasKey('Authorization', $client->header);
        $this->assertEquals('Basic VEVTVC1EQk0tTU5UOmVtcHR5cGFzc3dvcmQ=', $client->header['Authorization']);
    }

    // delete

    public function testClientGetsCorrectDeleteRequest()
    {
        $client = $this->getClient();
        $ripe   = new WebService($client);

        $person = new RegObject('FOO');
        $ripe->delete($person);

        $this->assertEquals('DELETE', $client->method);
        $this->assertEquals('https://rest-test.db.ripe.net/test/register/FOO', $client->url);
        $this->assertNull($client->body);
        $this->assertArrayHasKey('Authorization', $client->header);
        $this->assertEquals('Basic VEVTVC1EQk0tTU5UOmVtcHR5cGFzc3dvcmQ=', $client->header['Authorization']);
    }

    public function testClientGetsCorrectDeleteRequestWithReason()
    {
        $client = $this->getClient();
        $ripe   = new WebService($client);

        $person = new RegObject('FOO');
        $ripe->delete($person, 'because I can!');

        $this->assertEquals('DELETE', $client->method);
        $this->assertEquals('https://rest-test.db.ripe.net/test/register/FOO?reason=because%20I%20can%21', $client->url);
        $this->assertNull($client->body);
        $this->assertArrayHasKey('Authorization', $client->header);
        $this->assertEquals('Basic VEVTVC1EQk0tTU5UOmVtcHR5cGFzc3dvcmQ=', $client->header['Authorization']);
    }
}
