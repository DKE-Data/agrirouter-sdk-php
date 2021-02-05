<?php

namespace Lib\Tests\Helper {

    use App\Api\Messaging\HttpClientInterface;
    use GuzzleHttp\Client;
    use GuzzleHttp\Psr7\Request;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    /**
     * HTTP client implementation to wrap the Guzzle HTTP client.
     * @package Lib\Tests\Helper
     */
    class HttpClient implements HttpClientInterface
    {
        private Client $httpClient;

        /**
         * Constructor.
         * @param Client $httpClient -
         */
        public function __construct(Client $httpClient)
        {
            $this->httpClient = $httpClient;
        }

        public function createRequest(string $method, string $uri, array $headers = [], string $body = null): RequestInterface
        {
            return new Request($method, $uri, $headers, $body);
        }

        public function sendRequest(RequestInterface $request, array $options = []): ResponseInterface
        {
            $result = null;
            $promise = $this->httpClient->sendAsync($request, $options)->
            then(function ($response) {
                return $response;
            }, function ($response) {
                throw $response;
            });
            return $promise->wait(true);
        }
    }
}