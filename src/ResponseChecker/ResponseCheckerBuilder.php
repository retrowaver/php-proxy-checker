<?php

namespace Retrowaver\ProxyChecker\ResponseChecker;

use Psr\Http\Message\ResponseInterface;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;

class ResponseCheckerBuilder implements ResponseCheckerInterface
{
    protected $constraints = [];

    public function checkResponse(
        ResponseInterface $response,
        ProxyInterface $proxy
    ): bool {
        foreach ($this->constraints as $callable) {
            if (!$callable($response, $proxy)) {
                return false;
            }
        }

        return true;
    }

    public function bodyContains(string $phrase): self
    {
        $this->constraints[] = function (ResponseInterface $response, ProxyInterface $proxy) use ($phrase) {
            return (strpos((string)$response->getBody(), $phrase) !== false);
        };

        return $this;
    }

    public function bodyContainsProxyIp(): self
    {
        $this->constraints[] = function (ResponseInterface $response, ProxyInterface $proxy) {
            return (strpos((string)$response->getBody(), $proxy->getIp()) !== false);
        };

        return $this;
    }
}
