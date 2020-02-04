<?php

namespace Retrowaver\ProxyChecker\ResponseChecker;

use Psr\Http\Message\ResponseInterface;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;

class ResponseCheckerBuilder implements ResponseCheckerInterface
{
    /**
     * @var \Closure[]
     */
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

    /**
     * Adds a constraint that checks whether phrase `$phrase` occurs in response body
     * 
     * @param string $phrase
     * @return self
     */
    public function bodyContains(string $phrase): self
    {
        $this->constraints[] = function (ResponseInterface $response, ProxyInterface $proxy) use ($phrase) {
            return (strpos((string)$response->getBody(), $phrase) !== false);
        };

        return $this;
    }

    /**
     * Adds a constraint that checks whether proxy's ip occurs in response body
     * 
     * @return self
     */
    public function bodyContainsProxyIp(): self
    {
        $this->constraints[] = function (ResponseInterface $response, ProxyInterface $proxy) {
            return (strpos((string)$response->getBody(), $proxy->getIp()) !== false);
        };

        return $this;
    }
}
