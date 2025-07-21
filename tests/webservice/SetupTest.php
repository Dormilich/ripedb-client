<?php

use Dormilich\WebService\Adapter\ClientAdapter;
use Dormilich\WebService\RIPE\WebService;
use PHPUnit\Framework\TestCase;

class SetupTest extends TestCase
{
    public function testDefaultCredentials()
    {
        $client = $this->createMock(ClientAdapter::class);
        $client
            ->expects($this->once())
            ->method('setBaseUri')
            ->with(
                $this->identicalTo(WebService::SANDBOX_HOST)
            );
        $service = new WebService($client);

        $this->assertSame('TEST-DBM-MNT', $service->getUsername());
        $this->assertSame('emptypassword', $service->getPassword());
        $this->assertSame('https://rest-test.db.ripe.net', $service->getHost());
        $this->assertFalse($service->isProduction(), 'Environment is production');
    }

    public function testSetProductionCredentials()
    {
        $client = $this->createMock(ClientAdapter::class);
        $client
            ->expects($this->once())
            ->method('setBaseUri')
            ->with(
                $this->identicalTo(WebService::PRODUCTION_HOST)
            );
        $service = new WebService($client, [
            'environment' => WebService::PRODUCTION,
            'username' => '01K0P1GRYDWERK0R8EN2SC612B',
            'password' => '3ddb489c-4c7c-46e2-8254-8654a041d733',
        ]);

        $this->assertSame('01K0P1GRYDWERK0R8EN2SC612B', $service->getUsername());
        $this->assertSame('3ddb489c-4c7c-46e2-8254-8654a041d733', $service->getPassword());
        $this->assertSame('https://rest.db.ripe.net', $service->getHost());
        $this->assertTrue($service->isProduction(), 'Environment is not production');
    }

    public function testSetTestCredentials()
    {
        $client = $this->createMock(ClientAdapter::class);
        $client
            ->expects($this->exactly(2))
            ->method('setBaseUri')
            ->withConsecutive(
                [$this->identicalTo(WebService::PRODUCTION_HOST)],
                [$this->identicalTo(WebService::SANDBOX_HOST)]
            );
        $service = new WebService($client, [
            'environment' => WebService::PRODUCTION,
            'username' => '01K0P1GRYDWERK0R8EN2SC612B',
            'password' => '3ddb489c-4c7c-46e2-8254-8654a041d733',
        ]);
        $service->setEnvironment(WebService::SANDBOX);

        $this->assertSame('TEST-DBM-MNT', $service->getUsername());
        $this->assertSame('emptypassword', $service->getPassword());
        $this->assertSame('https://rest-test.db.ripe.net', $service->getHost());
        $this->assertFalse($service->isProduction(), 'Environment is production');
    }

    public function testSetCustomCredentials()
    {
        $client = $this->createMock(ClientAdapter::class);
        $client
            ->expects($this->once())
            ->method('setBaseUri')
            ->with(
                $this->identicalTo('http://localhost')
            );
        $service = new WebService($client, [
            'environment' => 'local',
            'location' => 'http://localhost'
        ]);

        $this->assertEmpty($service->getUsername());
        $this->assertEmpty($service->getPassword());
        $this->assertSame('http://localhost', $service->getHost());
        $this->assertFalse($service->isProduction(), 'Environment is production');
    }

    public function testSetCredentialsViaUrl()
    {
        $url = 'https://01K0P1GRYDWERK0R8EN2SC612B:3ddb489c-4c7c-46e2-8254-8654a041d733@rest.db.ripe.net:666/ripe?mntner=TEST-MNT';
        $client = $this->createMock(ClientAdapter::class);
        $client
            ->expects($this->exactly(2))
            ->method('setBaseUri')
            ->withConsecutive(
                [$this->identicalTo(WebService::SANDBOX_HOST)],
                [$this->identicalTo('https://rest.db.ripe.net:666')]
            );
        $service = new WebService($client);
        $service->setHost($url);

        $this->assertSame('01K0P1GRYDWERK0R8EN2SC612B', $service->getUsername());
        $this->assertSame('3ddb489c-4c7c-46e2-8254-8654a041d733', $service->getPassword());
        $this->assertSame('https://rest.db.ripe.net:666', $service->getHost());
        $this->assertTrue($service->isProduction(), 'Environment is not production');
    }
}
