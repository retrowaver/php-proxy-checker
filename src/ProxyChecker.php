<?php

namespace Retrowaver\ProxyChecker;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Retrowaver\ProxyChecker\ResponseChecker\ResponseCheckerInterface;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\EachPromise;

class ProxyChecker
{
    protected $request;
    protected $responseChecker;
    protected $options;
    protected $requestOptions;
    protected $client;

    public function __construct(
        RequestInterface $request,
        ResponseCheckerInterface $responseChecker,
        ?array $options = [],
        ?array $requestOptions = [],
        ?ClientInterface $guzzle = null
    ) {
        $this->request = $request;
        $this->responseChecker = $responseChecker;
        $this->options = $options + $this->getDefaultOptions();
        $this->requestOptions = $requestOptions + $this->getDefaultRequestOptions();
        $this->client = $guzzle ?? new Client();
    }

    public function checkProxies(array $proxies): array
    {
        $proxyIndexMap = array_keys($proxies);
        $validProxies = [];

        $eachPromise = new EachPromise($this->getPromiseGenerator($proxies)(), [
            'concurrency' => $this->options['concurrency'],
            'fulfilled' => function ($response, $index) use ($proxies, &$validProxies, $proxyIndexMap) {
                $proxy = $proxies[$proxyIndexMap[$index]];
                if ($this->responseChecker->checkResponse($response, $proxy)) {
                    $validProxies[] = $proxy;
                }
            }
        ]);

        $eachPromise->promise()->wait();
        return $validProxies;
    }

    protected function getPromiseGenerator(array $proxies)
    {
        return function () use ($proxies) {
            foreach ($proxies as $proxy) {
                yield $this->client->sendAsync(
                    $this->request,
                    [
                        'proxy' => (string)$proxy,
                        'http_errors' => false
                    ] + $this->requestOptions
                );
            }
        };
    }

    protected function getDefaultOptions(): array
    {
        return [
            'concurrency' => 50
        ];
    }

    protected function getDefaultRequestOptions(): array
    {
        return [
            'timeout' => 20
        ];
    }
}
