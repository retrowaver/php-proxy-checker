<?php

namespace Retrowaver\ProxyChecker\Import;

use Retrowaver\ProxyChecker\Entity\Proxy;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;

class SimpleImporter
{
    public function import(array $proxyArray, ?string $protocol = 'http'): array
    {
        $proxies = [];
        foreach ($proxyArray as $row) {
            $proxies[] = $this->getProxyFromRow($row, $protocol);
        }
        return $proxies;
    }

    protected function getProxyFromRow(string $row, string $protocol): ProxyInterface
    {
        $row = explode(':', $row);
        return (new Proxy)
            ->setIp($row[0])
            ->setPort((int)$row[1])
            ->setProtocol($protocol)
        ;
    }
}
