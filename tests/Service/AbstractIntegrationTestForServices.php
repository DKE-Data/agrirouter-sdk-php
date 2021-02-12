<?php declare(strict_types=1);

namespace Lib\Tests\Service {

    use App\Environment\AbstractEnvironment;
    use App\Environment\QualityAssuranceEnvironment;
    use Lib\Tests\Helper\MonologLoggerBuilder;
    use PHPUnit\Framework\TestCase;
    use Psr\Log\LoggerInterface;
    use ReflectionClass;
    use ReflectionException;

    abstract class AbstractIntegrationTestForServices extends TestCase
    {
        protected LoggerInterface $logger;
        private AbstractEnvironment $environment;

        public function __construct()
        {
            parent::__construct();
            $this->environment = new QualityAssuranceEnvironment();
            $loggerBuilder = new MonologLoggerBuilder();

            try {
                $loggerChannelName = (new ReflectionClass(get_class($this)))->getShortName();
                $this->logger = $loggerBuilder->withTestConsoleDefaultValues($loggerChannelName)->build();
            } catch (ReflectionException $e) {
                $this->logger = $loggerBuilder->withTestConsoleDefaultValues("TestClass")->build();
                $this->logger->info("Could not determine short classname for class " . get_class($this) . ". Reason: " . $e->getMessage());
            }
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

