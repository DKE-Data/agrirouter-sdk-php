<?php

namespace Lib\Tests\Service\Messaging\Mqtt\RoutingDevice {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Direction;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\PushNotification;
    use App\Api\Builder\CapabilityBuilder;
    use App\Dto\Messaging\DecodedMessage;
    use App\Dto\Messaging\MessagingResult;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\MqttMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\CapabilityService;
    use App\Service\Parameters\CapabilityParameters;
    use Exception;
    use Lib\Tests\Applications\FarmingSoftware;
    use Lib\Tests\Applications\TelemetryPlatform;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\MonologLoggerBuilder;
    use Lib\Tests\Helper\MqttClient;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Helper\PhpMqttClientBuilder;
    use Lib\Tests\Helper\RoutingDeviceOnboardResponseBuilder;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;
    use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
    use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
    use PhpMqtt\Client\Exceptions\DataTransferException;
    use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
    use PhpMqtt\Client\Exceptions\RepositoryException;

    class RoutingDeviceMessagingTest extends AbstractIntegrationTestForServices
    {
        private static OnboardResponse $farmingSoftwareOnboardResponse;
        private static OnboardResponse $telemetryPlatformOnboardResponse;

        /**
         * @throws Exception
         */
        public static function setUpBeforeClass(): void
        {
            self::$farmingSoftwareOnboardResponse = OnboardResponseRepository::read(Identifier::FARMING_SOFTWARE_MQTT);
            self::$telemetryPlatformOnboardResponse = OnboardResponseRepository::read(Identifier::TELEMETRY_PLATFORM_MQTT);
        }

        /**
         * @covers \Lib\Tests\Helper\MqttClient::connect()
         * @throws Exception
         */
        function testGivenValidRoutingDeviceDataTheMqttClientShouldConnectAndDisconnectSuccessful()
        {
            $loggerBuilder = new MonologLoggerBuilder();
            $routerDeviceOnboardResponse = OnboardResponseRepository::read(Identifier::ROUTING_DEVICE_MQTT);
            $routerDeviceOnboardResponse->getConnectionCriteria()->setClientId($routerDeviceOnboardResponse->getDeviceAlternateId());

            $mqttClient = (new PhpMqttClientBuilder())
                ->withLogger($loggerBuilder->withTestConsoleDefaultValues("RoutingDeviceMqtt")->build())
                ->fromOnboardResponse($routerDeviceOnboardResponse)->build();

            self::assertNotNull($mqttClient);
            $mqttClient->connect($routerDeviceOnboardResponse);
            self::assertTrue($mqttClient->isConnected());

            $mqttClient->disconnect();
            self::assertFalse($mqttClient->isConnected());
        }

        /**
         * @covers \Lib\Tests\Helper\MqttClient::subscribe()
         * @throws Exception
         */
        function testGivenRoutingDeviceAndFarmingSoftwareMergedOnboardResponseMessageSendingOverTheRoutingDeviceShouldBeSuccessful()
        {
            $loggerBuilder = new MonologLoggerBuilder();
            $routerDeviceOnboardResponse = OnboardResponseRepository::read(Identifier::ROUTING_DEVICE_MQTT);
            $routerDeviceOnboardResponse->getConnectionCriteria()->setClientId($routerDeviceOnboardResponse->getDeviceAlternateId());
            $farmingSoftwareOnboardResponse = OnboardResponseRepository::read(Identifier::FARMING_SOFTWARE_MQTT);

            $mergedOnboardResponseBuilder = new RoutingDeviceOnboardResponseBuilder();
            $mergedOnboardResponse = $mergedOnboardResponseBuilder
                ->withRouterDeviceOnboardResponse($routerDeviceOnboardResponse)
                ->withClientOnboardResponse($farmingSoftwareOnboardResponse)
                ->build();

            $mqttClient = (new PhpMqttClientBuilder())
                ->withLogger($loggerBuilder->withTestConsoleDefaultValues("RoutingDeviceMqtt")->build())
                ->fromOnboardResponse($mergedOnboardResponse)->build();

            self::assertNotNull($mqttClient);
            $mqttClient->connect($mergedOnboardResponse);
            self::assertTrue($mqttClient->isConnected());

            $logger = $this->getLogger();
            $callbackException = null;

            $mqttClient->subscribe($mergedOnboardResponse->getConnectionCriteria()->getCommands(),
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
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);
                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);
                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });

            $this->sendTaskdataCapabilityMessage($mqttClient, FarmingSoftware::applicationId(), FarmingSoftware::certificationVersionId(), $mergedOnboardResponse);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: $mqttClient));

            if ($callbackException !== null) {
                throw($callbackException);
            }

            $mqttClient->unsubscribe($mergedOnboardResponse->getConnectionCriteria()->getCommands());
            $mqttClient->disconnect();
            self::assertFalse($mqttClient->isConnected());
        }

        /**
         * @param MqttClient $mqttClient
         * @param string $applicationId
         * @param string $certificationVersionId
         * @param OnboardResponse $onboardResponse
         * @param int $direction
         * @return MessagingResult
         */
        protected function sendTaskdataCapabilityMessage(MqttClient $mqttClient,
                                                         string $applicationId,
                                                         string $certificationVersionId,
                                                         OnboardResponse $onboardResponse,
                                                         int $direction = Direction::SEND_RECEIVE): MessagingResult
        {
            $capabilityService = new CapabilityService(new MqttMessagingService($mqttClient));
            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId($applicationId);
            $capabilityParameters->setCertificationVersionId($certificationVersionId);
            $capabilityParameters->setOnboardResponse($onboardResponse);
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTaskdata($direction)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            return $capabilityService->send($capabilityParameters);
        }

        /**
         * @covers \Lib\Tests\Helper\MqttClient::subscribe()
         * @throws Exception
         */
        function testGivenRoutingDeviceAndTelemetryPlatformMergedOnboardResponseMessageSendingOverTheRoutingDeviceShouldBeSuccessful()
        {
            $loggerBuilder = new MonologLoggerBuilder();
            $routerDeviceOnboardResponse = OnboardResponseRepository::read(Identifier::ROUTING_DEVICE_MQTT);
            $routerDeviceOnboardResponse->getConnectionCriteria()->setClientId($routerDeviceOnboardResponse->getDeviceAlternateId());
            $telemetryPlatformOnboardResponse = OnboardResponseRepository::read(Identifier::TELEMETRY_PLATFORM_MQTT);

            $mergedOnboardResponseBuilder = new RoutingDeviceOnboardResponseBuilder();
            $mergedOnboardResponse = $mergedOnboardResponseBuilder
                ->withRouterDeviceOnboardResponse($routerDeviceOnboardResponse)
                ->withClientOnboardResponse($telemetryPlatformOnboardResponse)
                ->build();

            $mqttClient = (new PhpMqttClientBuilder())
                ->withLogger($loggerBuilder->withTestConsoleDefaultValues("RoutingDeviceMqtt")->build())
                ->fromOnboardResponse($mergedOnboardResponse)->build();

            self::assertNotNull($mqttClient);
            $mqttClient->connect($mergedOnboardResponse);
            self::assertTrue($mqttClient->isConnected());

            $logger = $this->getLogger();
            $callbackException = null;

            $mqttClient->subscribe($mergedOnboardResponse->getConnectionCriteria()->getCommands(),
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
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);
                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);
                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });

            $this->sendTaskdataCapabilityMessage($mqttClient, TelemetryPlatform::applicationId(), TelemetryPlatform::certificationVersionId(), $mergedOnboardResponse);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: $mqttClient));

            if ($callbackException !== null) {
                throw($callbackException);
            }

            $mqttClient->unsubscribe($mergedOnboardResponse->getConnectionCriteria()->getCommands());
            $mqttClient->disconnect();
            self::assertFalse($mqttClient->isConnected());
        }

        /**
         * @covers \Lib\Tests\Helper\MqttClient::subscribe()
         * @throws Exception
         */
        function testGivenRoutingDeviceAndHttpTelemetryPlatformMergedOnboardResponseMessageSendingOverTheRoutingDeviceShouldBeSuccessful()
        {
            $loggerBuilder = new MonologLoggerBuilder();
            $routerDeviceOnboardResponse = OnboardResponseRepository::read(Identifier::ROUTING_DEVICE_MQTT);
            $routerDeviceOnboardResponse->getConnectionCriteria()->setClientId($routerDeviceOnboardResponse->getDeviceAlternateId());
            $telemetryPlatformOnboardResponse = OnboardResponseRepository::read(Identifier::TELEMETRY_PLATFORM_MQTT);

            $mergedOnboardResponseBuilder = new RoutingDeviceOnboardResponseBuilder();
            $mergedOnboardResponse = $mergedOnboardResponseBuilder
                ->withRouterDeviceOnboardResponse($routerDeviceOnboardResponse)
                ->withClientOnboardResponse($telemetryPlatformOnboardResponse)
                ->build();

            $mqttClient = (new PhpMqttClientBuilder())
                ->withLogger($loggerBuilder->withTestConsoleDefaultValues("RoutingDeviceMqtt")->build())
                ->fromOnboardResponse($mergedOnboardResponse)->build();

            self::assertNotNull($mqttClient);
            $mqttClient->connect($mergedOnboardResponse);
            self::assertTrue($mqttClient->isConnected());

            $logger = $this->getLogger();
            $callbackException = null;

            $mqttClient->subscribe($mergedOnboardResponse->getConnectionCriteria()->getCommands(),
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
                        /** @var DecodedMessage $decodedMessages */
                        $decodedMessage = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);
                        $logger->info("Message decoded :\n-> Header: {header}\n->Payload: {payload}",
                            [
                                'header' => $decodedMessage->getResponseEnvelope()->serializeToJsonString(),
                                'payload' => $decodedMessage->getResponsePayloadWrapper()->getDetails()->serializeToString()
                            ]);
                        self::assertTrue($decodedMessage->getResponseEnvelope()->getResponseCode() === 201);
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });

            $this->sendTaskdataCapabilityMessage($mqttClient, TelemetryPlatform::applicationId(), TelemetryPlatform::certificationVersionId(), $mergedOnboardResponse);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: $mqttClient));

            if ($callbackException !== null) {
                throw($callbackException);
            }

            $mqttClient->unsubscribe($mergedOnboardResponse->getConnectionCriteria()->getCommands());
            $mqttClient->disconnect();
            self::assertFalse($mqttClient->isConnected());
        }

        /**
         * @covers \Lib\Tests\Helper\MqttClient::subscribe()
         * @throws ConfigurationInvalidException
         * @throws ConnectingToBrokerFailedException
         * @throws DataTransferException
         * @throws ProtocolNotSupportedException
         * @throws Exception
         */
        function testGivenRoutingDeviceOnboardResponsesMessageSendingForFarmingSoftwareAndTelemetryPlatformOverTheRoutingDeviceShouldBeSuccessful()
        {
            $telemetryPlatformCallbackException = null;
            $farmingSoftwareCallbackException = null;
            $receivedTelemetryPlatformMessage = false;
            $receivedFarmingSoftwareMessage = false;
            $loggerBuilder = new MonologLoggerBuilder();
            $routerDeviceOnboardResponse = OnboardResponseRepository::read(Identifier::ROUTING_DEVICE_MQTT);
            $routerDeviceOnboardResponse->getConnectionCriteria()->setClientId($routerDeviceOnboardResponse->getDeviceAlternateId());

            $mqttClient = (new PhpMqttClientBuilder())
                ->withLogger($loggerBuilder->withTestConsoleDefaultValues("RoutingDeviceMqtt")->build())
                ->fromOnboardResponse($routerDeviceOnboardResponse)->build();

            self::assertNotNull($mqttClient);
            $mqttClient->connect($routerDeviceOnboardResponse);
            self::assertTrue($mqttClient->isConnected());

            $this->subscribeCapabilitiesMessageCallback($mqttClient, self::$farmingSoftwareOnboardResponse->getConnectionCriteria()->getCommands(),
                $farmingSoftwareCallbackException, $receivedFarmingSoftwareMessage);
            $this->subscribeCapabilitiesMessageCallback($mqttClient, self::$telemetryPlatformOnboardResponse->getConnectionCriteria()->getCommands(),
                $telemetryPlatformCallbackException, $receivedTelemetryPlatformMessage);

            $this->sendTaskdataCapabilityMessage($mqttClient, TelemetryPlatform::applicationId(), TelemetryPlatform::certificationVersionId(), self::$telemetryPlatformOnboardResponse);
            $this->sendTaskdataCapabilityMessage($mqttClient, FarmingSoftware::applicationId(), FarmingSoftware::certificationVersionId(), self::$farmingSoftwareOnboardResponse);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: $mqttClient));
            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: $mqttClient));

            $mqttClient->unsubscribe(self::$telemetryPlatformOnboardResponse->getConnectionCriteria()->getCommands());
            $mqttClient->unsubscribe(self::$farmingSoftwareOnboardResponse->getConnectionCriteria()->getCommands());
            $mqttClient->disconnect();
            if ($farmingSoftwareCallbackException !== null) {
                throw($farmingSoftwareCallbackException);
            } elseif ($telemetryPlatformCallbackException !== null) {
                throw($telemetryPlatformCallbackException);
            }
            self::assertTrue($receivedTelemetryPlatformMessage);
            self::assertTrue($receivedFarmingSoftwareMessage);
            self::assertFalse($mqttClient->isConnected());
        }

        /**
         * @param MqttClient $mqttClient
         * @param string $queueName
         * @param Exception|null $callbackException
         * @param bool $hasReceivedMessage
         * @throws DataTransferException
         * @throws RepositoryException
         */
        protected function subscribeCapabilitiesMessageCallback(MqttClient $mqttClient, string $queueName,
                                                                ?Exception &$callbackException, bool &$hasReceivedMessage)
        {
            $logger = $this->getLogger();
            $mqttClient->subscribe($queueName, function (string $topic, string $message) use (&$mqttClient, &$logger, &$callbackException, &$hasReceivedMessage) {
                $logger->info("We received a message on topic [{topic}]: {message}",
                    [
                        'topic' => $topic,
                        'message' => $message
                    ]);
                $hasReceivedMessage = true;
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
                } catch (Exception $exception) {
                    $callbackException = $exception;
                }
                $logger->info("Leaving callback...");
                $mqttClient->interrupt();
            });
        }
    }
}