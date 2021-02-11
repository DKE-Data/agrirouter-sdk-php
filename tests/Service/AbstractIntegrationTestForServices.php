<?php declare(strict_types=1);

namespace Lib\Tests\Service {

    use App\Environment\AbstractEnvironment;
    use App\Environment\QualityAssuranceEnvironment;
    use Lib\Tests\Helper\LoggerBuilder;
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
            $this->logger = LoggerBuilder::createConsoleLogger();
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

