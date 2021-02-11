<?php declare(strict_types=1);

namespace Lib\Tests\Service {

    use App\Environment\AbstractEnvironment;
    use App\Environment\QualityAssuranceEnvironment;
    use Lib\Tests\Helper\LoggerBuilder;
    use PHPUnit\Framework\TestCase;
    use Psr\Log\LoggerInterface;

    abstract class AbstractIntegrationTestForServices extends TestCase
    {
        private AbstractEnvironment $environment;
        private LoggerInterface $logger;

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

        public function getLogger(): LoggerInterface
        {
            return $this->logger;
        }
    }
}

