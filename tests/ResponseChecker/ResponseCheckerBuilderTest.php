<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Retrowaver\ProxyChecker\ResponseChecker\ResponseCheckerBuilder;
use Retrowaver\ProxyChecker\Entity\Proxy;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

final class ResponseCheckerBuilderTest extends TestCase
{
    public function setUp(): void
    {
        $this->proxy = (new Proxy)
            ->setIp('192.168.1.1')
            ->setPort(1080)
            ->setProtocol('http')
        ;
    }

    public function testCheckResponseReturnsTrueWithoutConstraints(): void
    {
        $responseCheckerBuilder = new ResponseCheckerBuilder;

        $this->assertEquals(
            true,
            $responseCheckerBuilder->checkResponse(
                $this->createStub(ResponseInterface::class),
                $this->proxy
            )
        );
    }

    public function testBodyContainsMakesCheckResponseReturnTrue()
    {
        $responseCheckerBuilder = new ResponseCheckerBuilder;
        $responseCheckerBuilder->bodyContains('phrase');

        $this->assertEquals(
            true,
            $responseCheckerBuilder->checkResponse(
                new Response(200, [], 'foo phrase bar'),
                $this->proxy
            )
        );
    }

    public function testBodyContainsMakesCheckResponseReturnFalse()
    {
        $responseCheckerBuilder = new ResponseCheckerBuilder;
        $responseCheckerBuilder->bodyContains('phrase');

        $this->assertEquals(
            false,
            $responseCheckerBuilder->checkResponse(
                new Response(200, [], 'something else'),
                $this->proxy
            )
        );
    }

    public function testBodyContainsProxyIpMakesCheckResponseReturnTrue()
    {
        $responseCheckerBuilder = new ResponseCheckerBuilder;
        $responseCheckerBuilder->bodyContainsProxyIp();

        $this->assertEquals(
            true,
            $responseCheckerBuilder->checkResponse(
                new Response(200, [], 'your ip is 192.168.1.1'),
                $this->proxy
            )
        );
    }

    public function testBodyContainsProxyIpMakesCheckResponseReturnFalse()
    {
        $responseCheckerBuilder = new ResponseCheckerBuilder;
        $responseCheckerBuilder->bodyContainsProxyIp();

        $this->assertEquals(
            false,
            $responseCheckerBuilder->checkResponse(
                new Response(200, [], 'your ip is 127.0.0.1'),
                $this->proxy
            )
        );
    }

    public function testTrueChainedMethodsMakeCheckResponseReturnTrue()
    {
        $responseCheckerBuilder = new ResponseCheckerBuilder;
        $responseCheckerBuilder
            ->bodyContainsProxyIp()
            ->bodyContains('phrase')
        ;

        $this->assertEquals(
            true,
            $responseCheckerBuilder->checkResponse(
                new Response(200, [], 'your ip is 192.168.1.1. this is a phrase'),
                (new Proxy)->setIp('192.168.1.1')
            )
        );
    }

    public function testFalseChainedMethodsMakeCheckResponseReturnFalse()
    {
        $responseCheckerBuilder = new ResponseCheckerBuilder;
        $responseCheckerBuilder
            ->bodyContainsProxyIp()
            ->bodyContains('ip is present, but this phrase is not')
        ;

        $this->assertEquals(
            false,
            $responseCheckerBuilder->checkResponse(
                new Response(200, [], 'your ip is 192.168.1.1'),
                (new Proxy)->setIp('192.168.1.1')
            )
        );
    }
}
