<?php

namespace Retrowaver\ProxyChecker\ResponseChecker;

use Psr\Http\Message\ResponseInterface;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;

interface ResponseCheckerInterface
{
    public function checkResponse(
        ResponseInterface $response,
        ProxyInterface $proxy
    ): bool;
}
