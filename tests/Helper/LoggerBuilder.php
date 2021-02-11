<?php declare(strict_types=1);

namespace Lib\Tests\Helper {

    use DateTimeZone;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use Monolog\Processor\PsrLogMessageProcessor;

    /**
     * The LoggerBuilder builds default logger for the tests.
     * @package Lib\Tests\Helper
     */
    class LoggerBuilder
    {
        /**
         * Builds the default console logger for the tests.
         * @return Logger A simple console logger.
         */
        public static function createConsoleLogger(): Logger
        {
            $processor = new PsrLogMessageProcessor();
            $logger = new Logger('MqttClientConsole');
            $dateFormat = "d.m.Y, H:i:s";
            $formatter = new LineFormatter(null, $dateFormat, true, true);
            $handler = new StreamHandler('php://stdout', Logger::INFO);
            $handler->setFormatter($formatter);
            $logger->pushProcessor($processor);
            $logger->pushHandler($handler);
            $logger->setTimezone(new DateTimeZone('Europe/Berlin'));
            return $logger;
        }
    }
}