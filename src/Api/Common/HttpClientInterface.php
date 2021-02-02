<?php declare(strict_types=1);


namespace App\Api\Common {

    use Exception;
    use Psr\Http\Client\ClientInterface;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    /**
     * Interface to handle external HttpClient implementations. PSR-7 standard is preferred but does not support asynchronous communication.
     * This interface maps the asynchronous send method for the api and provides an abstract factory method for client dependent PSR-7 request implementations.
     * @package App\Api\Common
     */
    interface HttpClientInterface extends ClientInterface
    {
        /**
         * Sends a PSR-7 request asynchronous to a web server and returns a PSR-7 response.
         * @param RequestInterface $request The PSR-7 request.
         * @param array $options The options for the request
         * @return ResponseInterface|null A PSR-7 response.
         * @throws Exception In case of error.
         */
        public function sendAsync(RequestInterface $request, array $options = []): ?ResponseInterface;

        /**
         * Creates a PSR-7 request with the given parameters.
         * @param string $method The HTTP method
         * @param string $uri The URI for the request
         * @param array $headers The request headers
         * @param string|null $body The request body
         * @return RequestInterface A PSR-7 request.
         * @throws Exception In case of error.
         */
        public function createRequest(string $method, string $uri, array $headers = [], string $body = null): RequestInterface;
    }
}