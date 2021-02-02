<?php declare(strict_types=1);

namespace Lib\Tests\Helper {

    use DateTimeZone;
    use GuzzleHttp\Client;
    use GuzzleHttp\HandlerStack;
    use GuzzleHttp\MessageFormatter;
    use GuzzleHttp\Middleware;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use Psr\Log\LoggerInterface;

    /**
     * Manages the GuzzleHttpClient with Logging.
     * @package Lib\Tests\Helper
     */
    class GuzzleHttpClientBuilder
    {

        private HttpClient $httpClient;

        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->httpClient = new HttpClient($this->createHttpClient());
        }

        /**
         * Creates a PSR compatible http client.
         * @return Client -
         */
        private function createHttpClient(): Client
        {
            $logger = self::createConsoleLogger();
            return new Client([
                'handler' => self::createHandlerStack($logger),
                'verify' => false
            ]);
        }

        /**
         * Creates a standard logging Handler for logging requests and responses to the console.
         * @return Logger The default console logger.
         */
        private function createConsoleLogger(): Logger
        {
            $logger = new Logger('HttpClientConsole');
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
        private function createHandlerStack(LoggerInterface $logger): HandlerStack
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

        /**
         * Get the HTTP client.
         * @return HttpClient -
         */
        public function build(): HttpClient
        {
            return $this->httpClient;
        }
    }
}