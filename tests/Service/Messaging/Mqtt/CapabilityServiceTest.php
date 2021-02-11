<?php

namespace Lib\Tests\Service\Messaging\Mqtt {

    use Agrirouter\Commons\Message;
    use Agrirouter\Commons\Messages;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Capability;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Direction;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\PushNotification;
    use App\Api\Builder\CapabilityBuilder;
    use App\Dto\Messaging\DecodedMessage;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\MqttMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\CapabilityService;
    use App\Service\Parameters\CapabilityParameters;
    use Exception;
    use Lib\Tests\Applications\FarmingSoftware;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\LoggerBuilder;
    use Lib\Tests\Helper\MqttClient;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Helper\PhpMqttClientBuilder;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;
    use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
    use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
    use PhpMqtt\Client\Exceptions\DataTransferException;
    use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
    use PhpMqtt\Client\Exceptions\RepositoryException;
    use Psr\Log\LoggerInterface;
    use function PHPUnit\Framework\assertNotNull;

    class CapabilityServiceTest extends AbstractIntegrationTestForServices
    {
        private static MqttClient $mqttClient;
        private static LoggerInterface $logger;
        private static OnboardResponse $onboardResponse;

        /**
         * @throws ConfigurationInvalidException
         * @throws ConnectingToBrokerFailedException
         * @throws ProtocolNotSupportedException
         * @throws Exception
         */
        public static function setUpBeforeClass(): void
        {
            self::$logger = LoggerBuilder::createConsoleLogger();
            self::$onboardResponse = OnboardResponseRepository::read(Identifier::FARMING_SOFTWARE_MQTT);
            self::$mqttClient = (new PhpMqttClientBuilder())
                ->withLogger(self::$logger)
                ->withOnboardResponse(self::$onboardResponse)->build();

            assertNotNull(self::$mqttClient);
            self::$mqttClient->connect();
            self::assertTrue(self::$mqttClient->isConnected());
        }

        /**
         * @throws DataTransferException
         */
        public static function tearDownAfterClass(): void
        {
            self::$mqttClient->disconnect();
            self::assertFalse(self::$mqttClient->isConnected());
        }

        /**
         * @covers CapabilityService::send()
         * @throws Exception
         */
        public function testGivenInvalidCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldStillAcceptTheMessage()
        {
            $logger = self::$logger;
            $mqttClient = self::$mqttClient;
            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);

                    self::assertNotEmpty($message, "No Message received from the agrirouter.");
                    $receivedMessage = json_decode($message, true);

                    $decodeMessagesService = new DecodeMessageService();
                    $decodedMessages = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);
                    self::assertNotNull($decodedMessages);
                    self::assertEquals(400, $decodedMessages->getResponseEnvelope()->getResponseCode());

                    /** @var Messages $decodedDetails */
                    $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
                    self::assertNotNull($decodedDetails);

                    $agrirouterMessages = $decodedDetails->getMessages();
                    self::assertEquals(1, $agrirouterMessages->count());

                    $iterator = $agrirouterMessages->getIterator();
                    /** @var Message $message */
                    foreach ($iterator as $message) {
                        self::assertEquals("Capability for That one is invalid! was ignored as it is not known to the certification.", $message->getMessage());
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });

            $capabilityService = new CapabilityService(new MqttMessagingService(self::$mqttClient));
            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(FarmingSoftware::applicationId());
            $capabilityParameters->setCertificationVersionId(FarmingSoftware::certificationVersionId());
            $capabilityParameters->setOnboardResponse(self::$onboardResponse);
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);
            $capability = new Capability();
            $capability->setDirection(Direction::SEND_RECEIVE);
            $capability->setTechnicalMessageType("That one is invalid!");
            $capabilities = [$capability];
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);

            SleepTimer::letTheAgrirouterProcessTheMessage(mqttClient: self::$mqttClient);
        }

        /**
         * @covers CapabilityService::send()
         * @throws Exception
         */
        function testGivenEmptyCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = self::$logger;
            $mqttClient = self::$mqttClient;
            $applicationMessageId = "";

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$applicationMessageId) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    self::assertNotEmpty($message, "No Message received from the agrirouter.");
                    $receivedMessage = json_decode($message, true);
                    $decodeMessagesService = new DecodeMessageService();
                    /** @var DecodedMessage $decodedMessages */
                    $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);
                    $this->getLogger()->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                        [
                            'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                            'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                        ]);
                    self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                    self::assertEquals($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageId);
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });

            $applicationMessageId = UuidService::newUuid();
            $capabilityService = new CapabilityService(new MqttMessagingService(self::$mqttClient));
            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId($applicationMessageId);
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(FarmingSoftware::applicationId());
            $capabilityParameters->setCertificationVersionId(FarmingSoftware::certificationVersionId());
            $capabilityParameters->setOnboardResponse(self::$onboardResponse);
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);
            $capabilityBuilder = new CapabilityBuilder();
            $capabilityParameters->setCapabilityParameters($capabilityBuilder->build());
            $capabilityService->send($capabilityParameters);

            SleepTimer::letTheAgrirouterProcessTheMessage(mqttClient: self::$mqttClient);
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @covers CapabilityService::send()
         * @throws Exception
         */
        function testGivenTaskDataCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = self::$logger;
            $mqttClient = self::$mqttClient;
            $applicationMessageIds = [UuidService::newUuid(), UuidService::newUuid(), UuidService::newUuid()];

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$applicationMessageIds) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    self::assertNotEmpty($message, "No Message received from the agrirouter.");
                    $receivedMessage = json_decode($message, true);

                    $decodeMessagesService = new DecodeMessageService();
                    /** @var DecodedMessage $decodedMessages */
                    $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);

                    $this->getLogger()->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                        [
                            'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                            'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                        ]);

                    self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                    self::assertContains($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds);
                    unset($applicationMessageIds[array_search($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds)]);

                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });

            $capabilityService = new CapabilityService(new MqttMessagingService(self::$mqttClient));
            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[0]);
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(FarmingSoftware::applicationId());
            $capabilityParameters->setCertificationVersionId(FarmingSoftware::certificationVersionId());
            $capabilityParameters->setOnboardResponse(self::$onboardResponse);
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);
            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTaskdata(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTaskdata(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[1]);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTaskdata(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[2]);
            $capabilityService->send($capabilityParameters);

            $counter = 0;
            do {
                SleepTimer::letTheAgrirouterProcessTheMessage(mqttClient: self::$mqttClient);
                $counter++;
            } while ($counter < 3 || sizeof($applicationMessageIds) > 0);
            self::assertEmpty($applicationMessageIds, "Not all sent messages have been processed.");
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @throws ConfigurationInvalidException
         * @throws ConnectingToBrokerFailedException
         */
        protected function setUp(): void
        {
            if (!self::$mqttClient->isConnected()) {
                self::$mqttClient->connect();
            }
            self::assertTrue(self::$mqttClient->isConnected());
        }

        /**
         * @throws ConfigurationInvalidException
         * @throws ConnectingToBrokerFailedException
         * @throws DataTransferException
         * @throws RepositoryException
         */
        protected function tearDown(): void
        {
            if (!self::$mqttClient->isConnected()) {
                self::$mqttClient->connect();
            }
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
            self::assertTrue(self::$mqttClient->isConnected());
        }

    }
}