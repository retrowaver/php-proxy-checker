<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Retrowaver\ProxyChecker\Entity\Proxy;

final class ProxyTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testToStringReturnsValidProxyString($string, $proxy)
    {
        $this->assertEquals($string, (string)$proxy);
    }

    public function provider()
    {
        return [
            [
                'http://127.0.0.1:1080',
                (new Proxy)
                    ->setIp('127.0.0.1')
                    ->setPort(1080)
                    ->setProtocol('http')
            ],
            [
                'socks5://192.168.1.1:666',
                (new Proxy)
                    ->setIp('192.168.1.1')
                    ->setPort(666)
                    ->setProtocol('socks5')
            ],
            [
                'http://user:pass@127.0.0.1:1080',
                (new Proxy)
                    ->setIp('127.0.0.1')
                    ->setPort(1080)
                    ->setProtocol('http')
                    ->setUsername('user')
                    ->setPassword('pass')
            ]
        ];
    }
}
