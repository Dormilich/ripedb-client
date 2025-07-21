<?php

use PHPUnit\Framework\TestCase;

use Dormilich\WebService\RIPE\WebService;
use Test\MockClient;
use Test\RegObject;

class SubmitTest extends TestCase
{
    public function getClient(): MockClient
    {
        return new MockClient('[]');
    }

    // create

    public function testClientGetsCorrectDefaultCreateRequest()
    {
        $client = $this->getClient();
        $ripe   = new WebService($client);
        $obj    = new RegObject('create');

        $ripe->create($obj);

        $expected = '{"objects":{"object":[{"source":{"id":"TEST"},"attributes":{"attribute":[{"name":"register","value":"create"},{"name":"mnt-by","value":"TEST-MNT"},{"name":"source","value":"TEST"}]}}]}}';

        $this->assertEquals('POST', $client->method);
        $this->assertEquals('https://rest-test.db.ripe.net/test/register', $client->url);
        $this->assertJsonStringEqualsJsonString($expected, $client->body);
        $this->assertArrayHasKey('Authorization', $client->header);
        $this->assertEquals('Basic VEVTVC1EQk0tTU5UOmVtcHR5cGFzc3dvcmQ=', $client->header['Authorization']);
    }

    public function testClientGetsCreateRequestWithFallbackAuth()
    {
        $client = $this->getClient();
        $ripe   = new WebService($client, [
            'environment' => WebService::PRODUCTION,
            'password' => 'a686fa46-1ac6-40d1-93ba-1c7c9842cea4',
        ]);
        $obj    = new RegObject('create');

        $ripe->create($obj);

        $expected = '{"objects":{"object":[{"source":{"id":"RIPE"},"attributes":{"attribute":[{"name":"register","value":"create"},{"name":"mnt-by","value":"TEST-MNT"},{"name":"source","value":"RIPE"}]}}]}}';

        $this->assertEquals('POST', $client->method);
        $this->assertEquals('https://rest.db.ripe.net/ripe/register', $client->url);
        $this->assertJsonStringEqualsJsonString($expected, $client->body);
        $this->assertArrayHasKey('Authorization', $client->header);
        $this->assertEquals('Basic VEVTVC1NTlQ6YTY4NmZhNDYtMWFjNi00MGQxLTkzYmEtMWM3Yzk4NDJjZWE0', $client->header['Authorization']);
    }

    // update

    public function testClientGetsCorrectDefaultUpdateRequest()
    {
        $client = $this->getClient();
        $ripe   = new WebService($client);
        $obj    = new RegObject('update');

        $ripe->update($obj);

        $expected = '{"objects":{"object":[{"source":{"id":"TEST"},"attributes":{"attribute":[{"name":"register","value":"update"},{"name":"mnt-by","value":"TEST-MNT"},{"name":"source","value":"TEST"}]}}]}}';

        $this->assertEquals('PUT', $client->method);
        $this->assertEquals('https://rest-test.db.ripe.net/test/register/update', $client->url);
        $this->assertEquals($expected, $client->body);
        $this->assertArrayHasKey('Authorization', $client->header);
        $this->assertEquals('Basic VEVTVC1EQk0tTU5UOmVtcHR5cGFzc3dvcmQ=', $client->header['Authorization']);
    }

    public function testClientGetsUpdateRequestWithFallbackAuth()
    {
        $client = $this->getClient();
        $ripe   = new WebService($client, [
            'environment' => WebService::PRODUCTION,
            'password' => 'a686fa46-1ac6-40d1-93ba-1c7c9842cea4',
        ]);
        $obj    = new RegObject('update');

        $ripe->create($obj);

        $expected = '{"objects":{"object":[{"source":{"id":"RIPE"},"attributes":{"attribute":[{"name":"register","value":"update"},{"name":"mnt-by","value":"TEST-MNT"},{"name":"source","value":"RIPE"}]}}]}}';

        $this->assertEquals('POST', $client->method);
        $this->assertEquals('https://rest.db.ripe.net/ripe/register', $client->url);
        $this->assertJsonStringEqualsJsonString($expected, $client->body);
        $this->assertArrayHasKey('Authorization', $client->header);
        $this->assertEquals('Basic VEVTVC1NTlQ6YTY4NmZhNDYtMWFjNi00MGQxLTkzYmEtMWM3Yzk4NDJjZWE0', $client->header['Authorization']);
    }

    // delete

    public function testClientGetsCorrectDefaultDeleteRequest()
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

    public function testClientGetsDeleteRequestWithFallbackAuth()
    {
        $client = $this->getClient();
        $ripe   = new WebService($client, [
            'environment' => WebService::PRODUCTION,
            'password' => 'a686fa46-1ac6-40d1-93ba-1c7c9842cea4',
        ]);
        $obj    = new RegObject('FOO');

        $ripe->delete($obj);

        $this->assertEquals('DELETE', $client->method);
        $this->assertEquals('https://rest.db.ripe.net/ripe/register/FOO', $client->url);
        $this->assertNull($client->body);
        $this->assertArrayHasKey('Authorization', $client->header);
        $this->assertEquals('Basic VEVTVC1NTlQ6YTY4NmZhNDYtMWFjNi00MGQxLTkzYmEtMWM3Yzk4NDJjZWE0', $client->header['Authorization']);
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
