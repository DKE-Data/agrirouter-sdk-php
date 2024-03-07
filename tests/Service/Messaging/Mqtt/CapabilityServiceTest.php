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
    use Lib\Tests\Helper\MonologLoggerBuilder;
    use Lib\Tests\Helper\MqttClient;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Helper\PhpMqttClientBuilder;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;
    use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
    use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
    use PhpMqtt\Client\Exceptions\DataTransferException;
    use PhpMqtt\Client\Exceptions\MqttClientException;
    use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
    use PhpMqtt\Client\Exceptions\ProtocolViolationException;
    use PhpMqtt\Client\Exceptions\RepositoryException;

    class CapabilityServiceTest extends AbstractIntegrationTestForServices
    {
        private static MqttClient $mqttClient;
        private static OnboardResponse $onboardResponse;

        /**
         * @throws ConfigurationInvalidException
         * @throws ConnectingToBrokerFailedException
         * @throws ProtocolNotSupportedException
         * @throws Exception
         */
        public static function setUpBeforeClass(): void
        {
            $loggerBuilder = new MonologLoggerBuilder();
            self::$onboardResponse = OnboardResponseRepository::read(Identifier::FARMING_SOFTWARE_MQTT);
            self::$mqttClient = (new PhpMqttClientBuilder())
                ->withLogger($loggerBuilder->withTestConsoleDefaultValues("PhpMqttClient")->build())
                ->fromOnboardResponse(self::$onboardResponse)->build();

            self::assertNotNull(self::$mqttClient);
            self::$mqttClient->connect(self::$onboardResponse);
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
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         * @throws RepositoryException
         * @throws Exception
         */
        public function testGivenInvalidCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldStillAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $callbackException = null;
            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
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
                    } catch (Exception $exception) {
                        $callbackException = $exception;
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

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
            if ($callbackException !== null) {
                throw($callbackException);
            }
        }

        /**
         * @covers CapabilityService::send()
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws RepositoryException
         * @throws Exception
         */
        function testGivenEmptyCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $applicationMessageId = "";
            $callbackException = null;

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$applicationMessageId) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
                        self::assertNotEmpty($message, "No Message received from the agrirouter.");
                        $receivedMessage = json_decode($message, true);
                        $decodeMessagesService = new DecodeMessageService();
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);
                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);
                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                        self::assertEquals($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageId);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
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

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
            if ($callbackException !== null) {
                throw($callbackException);
            }
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @covers CapabilityService::send()
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws RepositoryException
         * @throws Exception
         */
        function testGivenTaskDataCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $callbackException = null;
            $applicationMessageIds = [UuidService::newUuid(), UuidService::newUuid(), UuidService::newUuid()];

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$applicationMessageIds) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
                        self::assertNotEmpty($message, "No Message received from the agrirouter.");
                        $receivedMessage = json_decode($message, true);

                        $decodeMessagesService = new DecodeMessageService();
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);

                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);

                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                        self::assertContains($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds);
                        unset($applicationMessageIds[array_search($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds)]);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
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

            $counter = 0;
            do {
                self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
                $counter++;
                if ($callbackException !== null) {
                    throw($callbackException);
                }
            } while ($counter < 3 || sizeof($applicationMessageIds) > 0);
            self::assertEmpty($applicationMessageIds, "Not all sent messages have been processed.");
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @covers CapabilityService::send()
         * @throws DataTransferException
         * @throws RepositoryException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         * @throws Exception
         */
        function testGivenDeviceDescriptionCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $callbackException = null;
            $applicationMessageIds = [UuidService::newUuid(), UuidService::newUuid(), UuidService::newUuid()];

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$applicationMessageIds) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
                        self::assertNotEmpty($message, "No Message received from the agrirouter.");
                        $receivedMessage = json_decode($message, true);

                        $decodeMessagesService = new DecodeMessageService();
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);

                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);

                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                        self::assertContains($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds);
                        unset($applicationMessageIds[array_search($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds)]);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
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
            $capabilities = $capabilityBuilder->withDeviceDescription(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withDeviceDescription(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[1]);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withDeviceDescription(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[2]);
            $capabilityService->send($capabilityParameters);

            $counter = 0;
            do {
                self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
                $counter++;
                if ($callbackException !== null) {
                    throw($callbackException);
                }
            } while ($counter < 3 || sizeof($applicationMessageIds) > 0);
            self::assertEmpty($applicationMessageIds, "Not all sent messages have been processed.");
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @covers CapabilityService::send()
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         * @throws RepositoryException
         * @throws Exception
         */
        function testGivenTimeLogCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $callbackException = null;
            $applicationMessageIds = [UuidService::newUuid(), UuidService::newUuid(), UuidService::newUuid()];

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$applicationMessageIds) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
                        self::assertNotEmpty($message, "No Message received from the agrirouter.");
                        $receivedMessage = json_decode($message, true);

                        $decodeMessagesService = new DecodeMessageService();
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);

                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);

                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                        self::assertContains($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds);
                        unset($applicationMessageIds[array_search($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds)]);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
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
            $capabilities = $capabilityBuilder->withTimeLog(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTimeLog(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[1]);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTimeLog(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[2]);
            $capabilityService->send($capabilityParameters);

            $counter = 0;
            do {
                self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
                $counter++;
                if ($callbackException !== null) {
                    throw($callbackException);
                }
            } while ($counter < 3 || sizeof($applicationMessageIds) > 0);
            self::assertEmpty($applicationMessageIds, "Not all sent messages have been processed.");
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @covers CapabilityService::send()
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         * @throws RepositoryException
         * @throws Exception
         */
        function testGivenImageCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $callbackException = null;
            $applicationMessageIds = [UuidService::newUuid(), UuidService::newUuid(), UuidService::newUuid()];

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$applicationMessageIds) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
                        self::assertNotEmpty($message, "No Message received from the agrirouter.");
                        $receivedMessage = json_decode($message, true);

                        $decodeMessagesService = new DecodeMessageService();
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);

                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);

                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                        self::assertContains($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds);
                        unset($applicationMessageIds[array_search($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds)]);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
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
            $capabilities = $capabilityBuilder->withBmp(Direction::SEND)
                ->withJpg(Direction::SEND)
                ->withPng(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withBmp(Direction::RECEIVE)
                ->withJpg(Direction::RECEIVE)
                ->withPng(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[1]);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withBmp(Direction::SEND_RECEIVE)
                ->withJpg(Direction::SEND_RECEIVE)
                ->withPng(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[2]);
            $capabilityService->send($capabilityParameters);

            $counter = 0;
            do {
                self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
                $counter++;
                if ($callbackException !== null) {
                    throw($callbackException);
                }
            } while ($counter < 3 || sizeof($applicationMessageIds) > 0);
            self::assertEmpty($applicationMessageIds, "Not all sent messages have been processed.");
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @covers CapabilityService::send()
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         * @throws RepositoryException
         * @throws Exception
         */
        function testGivenVideoCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $callbackException = null;
            $applicationMessageIds = [UuidService::newUuid(), UuidService::newUuid(), UuidService::newUuid()];

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$applicationMessageIds) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
                        self::assertNotEmpty($message, "No Message received from the agrirouter.");
                        $receivedMessage = json_decode($message, true);

                        $decodeMessagesService = new DecodeMessageService();
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);

                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);

                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                        self::assertContains($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds);
                        unset($applicationMessageIds[array_search($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds)]);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
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
            $capabilities = $capabilityBuilder->withAvi(Direction::SEND)
                ->withMp4(Direction::SEND)
                ->withWmv(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withAvi(Direction::RECEIVE)
                ->withMp4(Direction::RECEIVE)
                ->withWmv(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[1]);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withAvi(Direction::SEND_RECEIVE)
                ->withMp4(Direction::SEND_RECEIVE)
                ->withWmv(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[2]);
            $capabilityService->send($capabilityParameters);

            $counter = 0;
            do {
                self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
                $counter++;
                if ($callbackException !== null) {
                    throw($callbackException);
                }
            } while ($counter < 3 || sizeof($applicationMessageIds) > 0);
            self::assertEmpty($applicationMessageIds, "Not all sent messages have been processed.");
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @covers CapabilityService::send()
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         * @throws RepositoryException
         * @throws Exception
         */
        function testGivenShapeCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $callbackException = null;
            $applicationMessageIds = [UuidService::newUuid(), UuidService::newUuid(), UuidService::newUuid()];

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$applicationMessageIds) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
                        self::assertNotEmpty($message, "No Message received from the agrirouter.");
                        $receivedMessage = json_decode($message, true);

                        $decodeMessagesService = new DecodeMessageService();
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);

                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);

                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                        self::assertContains($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds);
                        unset($applicationMessageIds[array_search($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds)]);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
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
            $capabilities = $capabilityBuilder->withShape(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withShape(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[1]);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withShape(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[2]);
            $capabilityService->send($capabilityParameters);

            $counter = 0;
            do {
                self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
                $counter++;
                if ($callbackException !== null) {
                    throw($callbackException);
                }
            } while ($counter < 3 || sizeof($applicationMessageIds) > 0);
            self::assertEmpty($applicationMessageIds, "Not all sent messages have been processed.");
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @covers CapabilityService::send()
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         * @throws RepositoryException
         * @throws Exception
         */
        function testGivenPdfCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $callbackException = null;
            $applicationMessageIds = [UuidService::newUuid(), UuidService::newUuid(), UuidService::newUuid()];

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$applicationMessageIds) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
                        self::assertNotEmpty($message, "No Message received from the agrirouter.");
                        $receivedMessage = json_decode($message, true);

                        $decodeMessagesService = new DecodeMessageService();
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);

                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);

                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                        self::assertContains($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds);
                        unset($applicationMessageIds[array_search($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds)]);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
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
            $capabilities = $capabilityBuilder->withPdf(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withPdf(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[1]);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withPdf(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[2]);
            $capabilityService->send($capabilityParameters);

            $counter = 0;
            do {
                self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
                $counter++;
                if ($callbackException !== null) {
                    throw($callbackException);
                }
            } while ($counter < 3 || sizeof($applicationMessageIds) > 0);
            self::assertEmpty($applicationMessageIds, "Not all sent messages have been processed.");
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @covers CapabilityService::send()
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         * @throws RepositoryException
         * @throws Exception
         */
        function testGivenGpsInfoCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $callbackException = null;
            $applicationMessageIds = [UuidService::newUuid(), UuidService::newUuid(), UuidService::newUuid()];

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$applicationMessageIds) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
                        self::assertNotEmpty($message, "No Message received from the agrirouter.");
                        $receivedMessage = json_decode($message, true);

                        $decodeMessagesService = new DecodeMessageService();
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);

                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);

                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                        self::assertContains($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds);
                        unset($applicationMessageIds[array_search($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds)]);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
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
            $capabilities = $capabilityBuilder->withGpsInfo(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withGpsInfo(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[1]);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withGpsInfo(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[2]);
            $capabilityService->send($capabilityParameters);

            $counter = 0;
            do {
                self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
                $counter++;
                if ($callbackException !== null) {
                    throw($callbackException);
                }
            } while ($counter < 3 || sizeof($applicationMessageIds) > 0);
            self::assertEmpty($applicationMessageIds, "Not all sent messages have been processed.");
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
        }

        /**
         * @covers CapabilityService::send()
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         * @throws RepositoryException
         * @throws Exception
         */
        function testGivenAllCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $logger = $this->getLogger();
            $mqttClient = self::$mqttClient;
            $callbackException = null;
            $applicationMessageIds = [UuidService::newUuid(), UuidService::newUuid(), UuidService::newUuid()];

            self::$mqttClient->subscribe(self::$onboardResponse->getConnectionCriteria()->getCommands(),
                function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$applicationMessageIds) {
                    $logger->info("We received a message on topic [{topic}]: {message}",
                        [
                            'topic' => $topic,
                            'message' => $message
                        ]);
                    try {
                        self::assertNotEmpty($message, "No Message received from the agrirouter.");
                        $receivedMessage = json_decode($message, true);

                        $decodeMessagesService = new DecodeMessageService();
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);

                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);

                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                        self::assertContains($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds);
                        unset($applicationMessageIds[array_search($decodedMessage->getResponseEnvelope()->getApplicationMessageId(), $applicationMessageIds)]);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
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
            $capabilities = $capabilityBuilder
                ->withTaskdata(Direction::SEND)
                ->withDeviceDescription(Direction::SEND)
                ->withTimeLog(Direction::SEND)
                ->withBmp(Direction::SEND)
                ->withJpg(Direction::SEND)
                ->withPng(Direction::SEND)
                ->withShape(Direction::SEND)
                ->withPdf(Direction::SEND)
                ->withAvi(Direction::SEND)
                ->withMp4(Direction::SEND)
                ->withWmv(Direction::SEND)
                ->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder
                ->withTaskdata(Direction::RECEIVE)
                ->withDeviceDescription(Direction::RECEIVE)
                ->withTimeLog(Direction::RECEIVE)
                ->withBmp(Direction::RECEIVE)
                ->withJpg(Direction::RECEIVE)
                ->withPng(Direction::RECEIVE)
                ->withShape(Direction::RECEIVE)
                ->withPdf(Direction::RECEIVE)
                ->withAvi(Direction::RECEIVE)
                ->withMp4(Direction::RECEIVE)
                ->withWmv(Direction::RECEIVE)
                ->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[1]);
            $capabilityService->send($capabilityParameters);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder
                ->withTaskdata(Direction::SEND_RECEIVE)
                ->withDeviceDescription(Direction::SEND_RECEIVE)
                ->withTimeLog(Direction::SEND_RECEIVE)
                ->withBmp(Direction::SEND_RECEIVE)
                ->withJpg(Direction::SEND_RECEIVE)
                ->withPng(Direction::SEND_RECEIVE)
                ->withShape(Direction::SEND_RECEIVE)
                ->withPdf(Direction::SEND_RECEIVE)
                ->withAvi(Direction::SEND_RECEIVE)
                ->withMp4(Direction::SEND_RECEIVE)
                ->withWmv(Direction::SEND_RECEIVE)
                ->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityParameters->setApplicationMessageId($applicationMessageIds[2]);
            $capabilityService->send($capabilityParameters);

            $counter = 0;
            do {
                self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
                $counter++;
                if ($callbackException !== null) {
                    throw($callbackException);
                }
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
                self::$mqttClient->connect(self::$onboardResponse);
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
                self::$mqttClient->connect(self::$onboardResponse);
            }
            self::$mqttClient->unsubscribe(self::$onboardResponse->getConnectionCriteria()->getCommands());
            self::assertTrue(self::$mqttClient->isConnected());
        }
    }
}
