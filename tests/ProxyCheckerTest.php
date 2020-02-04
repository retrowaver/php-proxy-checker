<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Retrowaver\ProxyChecker\ProxyChecker;
use Psr\Http\Message\RequestInterface;
use Retrowaver\ProxyChecker\ResponseChecker\ResponseCheckerInterface;
use Retrowaver\ProxyChecker\Entity\Proxy;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;

final class ProxyCheckerTest extends TestCase
{
    public function setUp(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'Hello, World'),
            new Response(200, [], 'Hello, World')
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handlerStack]);

        //
        $this->proxy1 = (new Proxy)
            ->setIp('192.168.1.1')
            ->setPort(1080)
            ->setProtocol('http')
        ;
        $this->proxy2 = (new Proxy)
            ->setIp('192.168.1.2')
            ->setPort(1080)
            ->setProtocol('http')
        ;
    }
    
    public function testCheckProxiesReturnsAllProxiesWithTrueResponseChecker()
    {
        $trueResponseChecker = $this->createStub(ResponseCheckerInterface::class);
        $trueResponseChecker->method('checkResponse')->willReturn(true);

        $proxyChecker = new ProxyChecker(
            new Request('GET', 'http://example.com'),
            $trueResponseChecker,
            [],
            [],
            $this->client
        );

        $validProxies = $proxyChecker->checkProxies([
            5 => $this->proxy1,
            8 => $this->proxy2
        ]);

        $this->assertContains($this->proxy1, $validProxies);
        $this->assertContains($this->proxy2, $validProxies);
        $this->assertCount(2, $validProxies);
    }
    
    public function testCheckProxiesReturnsEmptyArrayWithFalseResponseChecker()
    {
        $falseResponseChecker = $this->createStub(ResponseCheckerInterface::class);
        $falseResponseChecker->method('checkResponse')->willReturn(false);

        $proxyChecker = new ProxyChecker(
            new Request('GET', 'http://example.com'),
            $falseResponseChecker,
            [],
            [],
            $this->client
        );

        $validProxies = $proxyChecker->checkProxies([
            5 => $this->proxy1,
            8 => $this->proxy2
        ]);

        $this->assertEquals([], $validProxies);
    }
}