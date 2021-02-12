<?php declare(strict_types=1);

namespace Lib\Tests\Helper {

    use DateTimeZone;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\HandlerInterface;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use Monolog\Processor\ProcessorInterface;
    use Monolog\Processor\PsrLogMessageProcessor;
    use Psr\Log\LoggerInterface;

    /**
     * The MonologLoggerBuilder builds a psr compatible logger with the Monolog logger implementation.
     * @package Lib\Tests\Helper
     */
    class MonologLoggerBuilder
    {
        private string $channelName;
        private ProcessorInterface $processor;
        private HandlerInterface $handler;
        private DateTimeZone $timeZone;

        /**
         * Sets the default values for creating a test logger.
         * @param string $channelName The channel name to use for the output
         * @return $this -
         */
        public function withTestConsoleDefaultValues(string $channelName): self
        {
            $this->channelName = $channelName;
            $this->processor = new PsrLogMessageProcessor();
            $dateFormat = "d.m.Y, H:i:s";
            $formatter = new LineFormatter(null, $dateFormat, true, true);
            $this->handler = new StreamHandler('php://stdout', Logger::INFO);
            $this->handler->setFormatter($formatter);
            $this->timeZone = new DateTimeZone('Europe/Berlin');
            return $this;
        }

        /**
         * Builds the final logger.
         * @return LoggerInterface The psr compatible logger.
         */
        public function build(): LoggerInterface
        {
            $logger = new Logger($this->channelName);
            $logger->pushProcessor($this->processor);
            $logger->pushHandler($this->handler);
            $logger->setTimezone($this->timeZone);
            return $logger;
        }
    }
}