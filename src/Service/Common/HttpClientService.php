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

    protected function createHandlerStack()
    {
        $stack = HandlerStack::create();
        //$stack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
        return $this->createLoggingHandlerStack($stack);
    }

    protected function createLoggingHandlerStack(HandlerStack $stack)
    {
        $messageFormats = [
            '{method} {uri} HTTP/{version}',
            'HEADERS: {req_headers}',
            'BODY: {req_body}',
            'RESPONSE: {code} - {res_body}',
        ];
        foreach ($messageFormats as $messageFormat) {
            // We'll use unshift instead of push, to add the middleware to the bottom of the stack, not the top
            $stack->unshift(
                $this->createGuzzleLoggingMiddleware($messageFormat)
            );
        }

        return $stack;
    }

    protected function createGuzzleLoggingMiddleware(string $messageFormat)
    {
        return Middleware::log(
            $this->logger,
            new MessageFormatter($messageFormat)
        );
    }

    /**
     * @return Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

}