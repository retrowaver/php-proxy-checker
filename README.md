# PHP Proxy Checker
Proxy Checker is a PHP library that allows you to quickly check a list of proxies.
- fast (thanks to asynchronous requests)
- simple (PSR-7 based)
- supports many protocols
- customizable (full control over request / response checking)

## How it works?
`ProxyChecker` constructor takes 2 mandatory arguments:
- PSR-7 Request
- object that implements `ResponseCheckerInterface`

When `checkProxies()` is called, it attempts to send that request using every proxy, and then calls `checkResponse()` of provided `ResponseCheckerInterface` implementation, which ultimately decides whether proxy is valid or not.

Depending on how much control you want, you can use built-in `ResponseCheckerBuilder` for a quick start (see below) or make a custom class.

## 1. Basic usage

### Step 1. Make proxy array
Make proxy array manually:
```php
use Retrowaver\ProxyChecker\Entity\Proxy;

$proxies = [
    (new Proxy)
        ->setIp('127.0.0.1')
        ->setPort(1080)
        ->setProtocol('http'),
    (new Proxy)
        ->setIp('192.168.1.1')
        ->setPort(8080)
        ->setProtocol('http')
];
```

... or use built-in simple importer:
```php
use Retrowaver\ProxyChecker\Import\SimpleImporter;

$importer = new SimpleImporter;

$lines = file('path-to-file-with-proxies.txt'); // ip:port format
$proxies = $importer->import($lines, 'http');
```

### Step 2. Prepare a request
Prepare a PSR-7 request that will be send using proxies.

```php
use GuzzleHttp\Psr7\Request;

$request = new Request('GET', 'http://example.com');
```

### Step 3. Prepare ResponseChecker
You can use built-in ResponseCheckerBuilder:
```php
use Retrowaver\ProxyChecker\ResponseChecker\ResponseCheckerBuilder;

$responseChecker = (new ResponseCheckerBuilder)
    ->bodyContains('some string on target website')
;
```

or write a custom `ResponseCheckerInterface` implementation:
```php
use Psr\Http\Message\ResponseInterface;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;

class CustomResponseChecker implements ResponseCheckerInterface
{
    public function checkResponse(
        ResponseInterface $response,
        ProxyInterface $proxy
    ): bool {
        if (...) {
            // proxy not valid
            return false;
        }

        // valid proxy
        return true;
    }
}
```

```php
$responseChecker = new CustomResponseChecker;
```

### Step 4. Create ProxyChecker and check proxies
```php
use Retrowaver\ProxyChecker\ProxyChecker;

$proxyChecker = new ProxyChecker($request, $responseChecker);

$validProxies = $proxyChecker->checkProxies($proxies);
```

## 2. Additional info
### Options reference
`ProxyChecker` accepts optional parameters `$options` and `$requestOptions`:
- `$options`
    - `concurrency` - max concurrent request (default 50)
- `$requestOptions` are [Guzzle request options](http://docs.guzzlephp.org/en/stable/request-options.html) that are passed to Guzzle client while sending a request. Currently there's only one default value: `'timeout' => 20`

### Supported protocols
PHP Proxy Checker should work with http, https, socks4, socks4a, socks5 and socks5h proxies (see https://curl.haxx.se/libcurl/c/CURLOPT_PROXY.html for descriptions).