<?php declare(strict_types=1);

namespace Lib\Tests\Service\Onboard {

    use App\Api\Common\HttpClient;
    use App\Environment\AbstractEnvironment;
    use App\Environment\QualityAssuranceEnvironment;
    use App\Service\Common\HttpClientService;
    use DateTimeZone;
    use Lib\Tests\Helper\GuzzleHttpClientFactory;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use PHPUnit\Framework\TestCase;

    abstract class AbstractIntegrationTestForServices extends TestCase
    {
        private AbstractEnvironment $environment;
        private HttpClient $httpClient;
        private Logger $logger;

        public function __construct()
        {
            parent::__construct();
            $this->environment = new QualityAssuranceEnvironment();
            $this->httpClient = new GuzzleHttpClientFactory();
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

        public function getEnvironment(): ?QualityAssuranceEnvironment
        {
            return $this->environment;
        }

        public function getHttpClient(): HttpClient
        {
            return $this->httpClient;
        }

        public function getLogger(): Logger
        {
            return $this->logger;
        }
    }
}

