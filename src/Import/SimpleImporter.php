<?php

namespace Retrowaver\ProxyChecker\Import;

use Retrowaver\ProxyChecker\Entity\Proxy;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;

class SimpleImporter
{
    /**
     * Takes an array of strings in ip:port format. Returns an array of Proxy objects
     * 
     * @param string[] $proxyArray
     * @param string|null $protocol
     * @return ProxyInterface[]
     */
    public function import(array $proxyArray, ?string $protocol = 'http'): array
    {
        $proxies = [];
        foreach ($proxyArray as $row) {
            $proxies[] = $this->getProxyFromRow($row, $protocol);
        }
        return $proxies;
    }

    /**
     * @param string $row proxy string in ip:port format
     * @param string $protocol
     * @return ProxyInterfafce
     */
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
