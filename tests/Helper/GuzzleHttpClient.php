<?php declare(strict_types=1);

namespace Lib\Tests\Helper {

    use App\Api\Common\HttpClient;
    use DateTimeZone;
    use GuzzleHttp\Client;
    use GuzzleHttp\HandlerStack;
    use GuzzleHttp\MessageFormatter;
    use GuzzleHttp\Middleware;
    use GuzzleHttp\Psr7\Request;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Log\LoggerInterface;

    /**
     * Manages the GuzzleHttpClient with Logging.
     * @package Lib\Tests\Helper
     */
    class GuzzleHttpClient implements HttpClient
    {
        private Client $httpClient;

        /**
         * Constructor.
         */
        public function __construct()
        {

            $this->httpClient = $this->createHttpClient();
        }

        /**
         * Creates a PSR compatible http client.
         * @param null $logger
         * @return Client
         */
        public function createHttpClient(?LoggerInterface $logger = null): Client
        {
            if (is_null($logger)) $logger = self::createConsoleLogger();

            $httpClient = new Client([
                'handler' => self::createHandlerStack($logger),
                'verify' => false
            ]);
            return $httpClient;
        }

        /**
         * Creates a standard logging Handler for logging requests and responses to the console.
         * @param string $channel Channel name for the logger.
         * @return Logger The default console logger.
         */
        private function createConsoleLogger(string $channel = 'HttpClientConsole'): Logger
        {
            $logger = new Logger($channel);
            $dateFormat = "d.m.Y, H:i:s";
            $formatter = new LineFormatter(null, $dateFormat, false, true);
            $handler = new StreamHandler('php://stdout', Logger::INFO);
            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);
            $logger->setTimezone(new DateTimeZone('Europe/Berlin'));
            return $logger;
        }

        /**
         * Creates the guzzle logging stack for logging requests/responses.
         * @param LoggerInterface $logger The psr logger to use for logging.
         * @return HandlerStack The prepared guzzle HandlerStack.
         */
        private function createHandlerStack(LoggerInterface $logger)
        {
            $stack = HandlerStack::create();

            $messageFormats = [
                '{method} {uri} HTTP/{version}',
                'HEADERS: {req_headers}',
                'BODY: {req_body}',
                'RESPONSE: {code} - {res_body}',
            ];
            foreach ($messageFormats as $messageFormat) {
                $stack->unshift(
                    Middleware::log(
                        $logger,
                        new MessageFormatter($messageFormat)
                    )
                );
            }

            return $stack;

        }

        public function createRequest(string $method, string $uri, array $headers = [], string $body = null): RequestInterface
        {
            return new Request($method, $uri, $headers, $body);
        }

        public function sendRequest(RequestInterface $request): ResponseInterface
        {
            return $this->sendRequest($request);
        }

        public function sendAsync(RequestInterface $request, array $options = []): ?ResponseInterface
        {
            $result = null;

            $promise = $this->httpClient->sendAsync($request, $options)->
            then(function ($response) {
                return (string)$response();
            }, function ($response) {
                throw $response;
            });
            return $promise->wait(true);
        }
    }
}