<?php

namespace Retrowaver\ProxyChecker;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Retrowaver\ProxyChecker\ResponseChecker\ResponseCheckerInterface;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Promise\PromiseInterface;

class ProxyChecker
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseCheckerInterface
     */
    protected $responseChecker;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $requestOptions;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param RequestInterface $request
     * @param ResponseCheckerInterface $responseChecker
     * @param array|null $options
     * @param array|null $requestOptions
     * @param ClientInterface|null $guzzle
     */
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

    /**
     * @param ProxyInterface[] $proxies
     * @return ProxyInterface[]
     */
    public function checkProxies(array $proxies): array
    {
        $proxyIndexMap = array_keys($proxies);
        $validProxies = [];

        $eachPromise = new EachPromise($this->getPromiseGenerator($proxies)(), [
            'concurrency' => $this->options['concurrency'],
            'fulfilled' => function (ResponseInterface $response, int $index) use ($proxies, &$validProxies, $proxyIndexMap): void {
                $proxy = $proxies[$proxyIndexMap[$index]];
                if ($this->responseChecker->checkResponse($response, $proxy)) {
                    $validProxies[] = $proxy;
                }
            }
        ]);

        $eachPromise->promise()->wait();
        return $validProxies;
    }

    /**
     * @param ProxyInterface[] $proxies
     * @return \Closure
     */
    protected function getPromiseGenerator(array $proxies): \Closure
    {
        return function () use ($proxies): \Generator {
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

    /**
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        return [
            'concurrency' => 50
        ];
    }

    /**
     * @return array
     */
    protected function getDefaultRequestOptions(): array
    {
        return [
            'timeout' => 20
        ];
    }
}
