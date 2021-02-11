<?php

namespace Lib\Tests\Service\Common {

    use Exception;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\LoggerBuilder;
    use Lib\Tests\Helper\MqttClient;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Helper\PhpMqttClientBuilder;
    use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
    use PHPUnit\Framework\TestCase;
    use function PHPUnit\Framework\assertNotNull;

    class MqttClientIntegrationTest extends TestCase
    {
        /**
         * @covers MqttClient::connect()
         * @throws ProtocolNotSupportedException
         * @throws Exception
         */
        public function testConnectMqttClientToFarmingSoftwareEndpoint()
        {
            $onboardResponse = OnboardResponseRepository::read(Identifier::FARMING_SOFTWARE_MQTT);
            assertNotNull($onboardResponse->getConnectionCriteria());

            $phpMqttClientBuilder = new PhpMqttClientBuilder();

            $phpMqttClient = $phpMqttClientBuilder
                ->withLogger(LoggerBuilder::createConsoleLogger())
                ->withOnboardResponse($onboardResponse)
                ->build();
            assertNotNull($phpMqttClient);

            $phpMqttClient->connect($onboardResponse);
            self::assertTrue($phpMqttClient->isConnected());

            $phpMqttClient->disconnect();
            self::assertFalse($phpMqttClient->isConnected());
        }

        /**
         * @covers MqttClient::connect()
         * @throws ProtocolNotSupportedException
         * @throws Exception
         */
        public function testConnectMqttClientToCommunicationUnitEndpoint()
        {
            $onboardResponse = OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_MQTT);
            assertNotNull($onboardResponse->getConnectionCriteria());

            $phpMqttClientBuilder = new PhpMqttClientBuilder();

            $phpMqttClient = $phpMqttClientBuilder
                ->withLogger(LoggerBuilder::createConsoleLogger())
                ->withOnboardResponse($onboardResponse)
                ->build();
            assertNotNull($phpMqttClient);

            $phpMqttClient->connect($onboardResponse);
            self::assertTrue($phpMqttClient->isConnected());

            $phpMqttClient->disconnect();
            self::assertFalse($phpMqttClient->isConnected());
        }

    }
}



