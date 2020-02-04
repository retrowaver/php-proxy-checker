<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Retrowaver\ProxyChecker\Entity\Proxy;
use Retrowaver\ProxyChecker\Import\SimpleImporter;

final class SimpleImporterTest extends TestCase
{
    public function setUp(): void
    {
        $this->simpleImporter = new SimpleImporter;
    }

    public function testImportReturnsProxiesWithValidData()
    {
        $proxyArray = [
            '127.0.0.1:1080'
        ];

        $proxies = $this->simpleImporter->import($proxyArray, 'socks5');
        $proxy = array_pop($proxies);

        $this->assertEquals(
            $proxy->getIp(),
            '127.0.0.1'
        );
        
        $this->assertEquals(
            $proxy->getPort(),
            '1080'
        );
        
        $this->assertEquals(
            $proxy->getProtocol(),
            'socks5'
        );
    }

    public function testImportReturnsValidAmountOfProxies()
    {
        $proxyArray = [
            '127.0.0.1:1080',
            '127.0.0.1:1080',
            '127.0.0.1:1080',
            '127.0.0.1:1080',
            '127.0.0.1:1080'
        ];

        $this->assertCount(
            count($proxyArray),
            $this->simpleImporter->import($proxyArray)
        );
    }
}
