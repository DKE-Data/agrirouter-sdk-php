<?php declare(strict_types=1);

namespace Lib\Tests\Service {

    use App\Environment\AbstractEnvironment;
    use App\Environment\QualityAssuranceEnvironment;
    use DateTimeZone;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use PHPUnit\Framework\TestCase;

    abstract class AbstractIntegrationTestForServices extends TestCase
    {
        private AbstractEnvironment $environment;
        private Logger $logger;

        public function __construct()
        {
            parent::__construct();
            $this->environment = new QualityAssuranceEnvironment();
            $this->logger = $this->createConsoleTestLogger();
        }

        private function createConsoleTestLogger(string $channel = 'TestConsole'): Logger
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

        public function getEnvironment(): QualityAssuranceEnvironment
        {
            return $this->environment;
        }

        public function getLogger(): Logger
        {
            return $this->logger;
        }
    }
}

