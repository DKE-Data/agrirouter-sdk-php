<?php

namespace Lib\Tests\Service\Messaging\Mqtt {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Capability;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Direction;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\PushNotification;
    use App\Service\Common\MqttMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\CapabilityService;
    use App\Service\Parameters\CapabilityParameters;
    use Exception;
    use Lib\Tests\Applications\FarmingSoftware;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\LoggerBuilder;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Helper\PhpMqttClientBuilder;
    use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
    use PHPUnit\Framework\TestCase;
    use function PHPUnit\Framework\assertNotNull;

    class CapabilitiesServiceTest extends TestCase
    {
        /**
         * @covers CapabilityService::send()
         * @throws ProtocolNotSupportedException
         * @throws Exception
         */
        public function testConnectMqttClientToFarmingSoftwareEndpoint()
        {
            $onboardResponse = OnboardResponseRepository::read(Identifier::FARMING_SOFTWARE_MQTT);
            assertNotNull($onboardResponse->getConnectionCriteria());
            $phpMqttClientBuilder = new PhpMqttClientBuilder($onboardResponse->getConnectionCriteria()->getHost(),
                $onboardResponse->getConnectionCriteria()->getPort(),
                $onboardResponse->getConnectionCriteria()->getClientId());
            $phpMqttClient = $phpMqttClientBuilder->build();
            assertNotNull($phpMqttClient);
            $phpMqttClient->connect($onboardResponse);
            self::assertTrue($phpMqttClient->isConnected());

            $logger = LoggerBuilder::createConsoleLogger();
            $receivedDecodedMessage = null;
            $phpMqttClient->registerMessageReceivedEventHandler($phpMqttClient->getHandler($logger, $receivedDecodedMessage));
            $phpMqttClient->subscribe($onboardResponse->getConnectionCriteria()->getCommands(), qualityOfService: 2);

            $capabilityService = new CapabilityService(new MqttMessagingService($phpMqttClient));
            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(FarmingSoftware::applicationId());
            $capabilityParameters->setCertificationVersionId(FarmingSoftware::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::FARMING_SOFTWARE_MQTT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);
            $capability = new Capability();
            $capability->setDirection(Direction::SEND_RECEIVE);
            $capability->setTechnicalMessageType("That one is invalid!");
            $capabilities = [];
            array_push($capabilities, $capability);
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilityService->send($capabilityParameters);
            assertNotNull($messagingResult);

            $phpMqttClient->wait();
            $resultMessagePayload = $receivedDecodedMessage->getResponsePayloadWrapper();
            $logger->info("Result message payload : " . $resultMessagePayload->getDetails()->getValue());
            self::assertStringContainsString('Capability for That one is invalid! was ignored as it is not known to the certification.', $resultMessagePayload->getDetails()->getValue());
            self::assertStringContainsString('VAL_000007', $resultMessagePayload->getDetails()->getValue());

            $phpMqttClient->unsubscribe($onboardResponse->getConnectionCriteria()->getCommands());
            $phpMqttClient->disconnect();
            self::assertFalse($phpMqttClient->isConnected());
        }
    }
}