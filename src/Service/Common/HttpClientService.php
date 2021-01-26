<?php declare(strict_types=1);

namespace App\Service\Common;


use DateTimeZone;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class HttpClientService - Manages the HttpClient with Logging
 * @package App\Service\Common
 */
class HttpClientService
{
    private Client $httpClient;
    private Logger $logger;

    public function __construct(?Logger $logger = null)
    {
        if (is_null($logger)) $this->logger = $this->createConsoleHandler();
        else  $this->logger = $logger;
        $this->httpClient = new Client([
            'handler' => $this->createHandlerStack(),
            'verify' => false
        ]);
    }

    /**
     * Creates a standard logging Handler for logging requests and responses to the console
     * @param string $channel
     * @return Logger
     */
    private function createConsoleHandler(string $channel = 'HttpClientConsole'):Logger
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
     * Creates the handler stack
     * @return HandlerStack
     */
    protected function createHandlerStack()
    {
        $stack = HandlerStack::create();
        return $this->createLoggingHandlerStack($stack);
    }

    /**
     * Cretes the guzzle logging stack for logging requests/responses
     * @param HandlerStack $stack
     * @return HandlerStack
     */
    protected function createLoggingHandlerStack(HandlerStack $stack)
    {
        $messageFormats = [
            '{method} {uri} HTTP/{version}',
            'HEADERS: {req_headers}',
            'BODY: {req_body}',
            'RESPONSE: {code} - {res_body}',
        ];
        foreach ($messageFormats as $messageFormat) {
            $stack->unshift(
                $this->createGuzzleLoggingMiddleware($messageFormat)
            );
        }

        return $stack;
    }

    /**
     * Creates the guzzle middleware for logging
     * @param string $messageFormat
     * @return callable
     */
    protected function createGuzzleLoggingMiddleware(string $messageFormat)
    {
        return Middleware::log(
            $this->logger,
            new MessageFormatter($messageFormat)
        );
    }

    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

}