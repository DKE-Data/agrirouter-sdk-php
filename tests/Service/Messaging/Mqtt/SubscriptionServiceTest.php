<?php

namespace Lib\Tests\Service\Messaging\Mqtt {

    use Agrirouter\Commons\Message;
    use Agrirouter\Commons\Messages;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Direction;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\PushNotification;
    use Agrirouter\Request\Payload\Endpoint\Subscription\MessageTypeSubscriptionItem;
    use App\Api\Builder\CapabilityBuilder;
    use App\Definitions\CapabilityTypeDefinitions;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\MqttMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\CapabilityService;
    use App\Service\Messaging\SubscriptionService;
    use App\Service\Parameters\CapabilityParameters;
    use App\Service\Parameters\SubscriptionParameters;
    use Exception;
    use Lib\Tests\Applications\CommunicationUnit;
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

    class SubscriptionServiceTest extends AbstractIntegrationTestForServices
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
            self::$onboardResponse = OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_MQTT);
            self::$mqttClient = (new PhpMqttClientBuilder())
                ->withLogger($loggerBuilder->withTestConsoleDefaultValues("PhpMqttClient")->build())
                ->fromOnboardResponse(self::$onboardResponse)->build();

            self::assertNotNull(self::$mqttClient);
            self::$mqttClient->connect(self::$onboardResponse);
            self::assertTrue(self::$mqttClient->isConnected());

            self::setCapabilitiesForTest();
            $cleanupQueues = true;
            self::cleanupQueues($cleanupQueues);
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
         * CleanUp the mqtt queue of the endpoint.
         * @param bool $cleanupQueues Indicates whether to (continue) clean up or not.
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         */
        protected static function cleanupQueues(bool $cleanupQueues): void
        {
            $loggerBuilder = new MonologLoggerBuilder();
            $logger = $loggerBuilder->withTestConsoleDefaultValues("TestClassSetup")->build();
            $counter = 0;
            while ($cleanupQueues) {
                $cleanupQueues = SleepTimer::letTheAgrirouterProcessTheMqttMessage(2, self::$mqttClient);
                if ($cleanupQueues) $counter++;
            }
            $logger->info("Cleaned mqtt queue, removed " . $counter . " messages.");
        }

        protected static function setCapabilitiesForTest(): void
        {
            $capabilityService = new CapabilityService(new MqttMessagingService(self::$mqttClient));
            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(self::$onboardResponse);
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);
            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTaskdata(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);
            $capabilityService->send($capabilityParameters);
        }

        /**
         * @covers SubscriptionService::send()
         * @throws Exception
         */
        function testGivenInvalidSubscriptionWhenSendingSubscriptionThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
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
                            self::assertEquals("Subscription to \"This one is invalid.\" is not valid per reported capabilities.", $message->getMessage());
                        }
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });
            $subscriptionService = new SubscriptionService(new MqttMessagingService(self::$mqttClient));
            $subscriptionParameters = new SubscriptionParameters();
            $subscriptionParameters->setApplicationMessageId(UuidService::newUuid());
            $subscriptionParameters->setApplicationMessageSeqNo(1);
            $subscriptionParameters->setOnboardResponse(self::$onboardResponse);
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType("This one is invalid.");
            $subscriptionItems = [];
            array_push($subscriptionItems, $subscriptionItem);
            $subscriptionParameters->setSubscriptionItems($subscriptionItems);
            $subscriptionService->send($subscriptionParameters);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(3, self::$mqttClient));
            if ($callbackException !== null) {
                throw($callbackException);
            }
        }

        /**
         * @covers SubscriptionService::send()
         * @throws Exception
         */
        function testGivenValidSubscriptionWhenSendingSubscriptionThenTheAgrirouterShouldAcceptTheMessage()
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
                        $receivedMessage = json_decode($message, true);
                        $decodeMessagesService = new DecodeMessageService();
                        $decodedMessages = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);
                        self::assertNotNull($decodedMessages);
                        self::assertEquals(201, $decodedMessages->getResponseEnvelope()->getResponseCode());
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });
            $subscriptionService = new SubscriptionService(new MqttMessagingService(self::$mqttClient));
            $subscriptionParameters = new SubscriptionParameters();
            $subscriptionParameters->setApplicationMessageId(UuidService::newUuid());
            $subscriptionParameters->setApplicationMessageSeqNo(1);
            $subscriptionParameters->setOnboardResponse(self::$onboardResponse);
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::ISO_11783_TASKDATA_ZIP);
            $subscriptionItems = [];
            array_push($subscriptionItems, $subscriptionItem);
            $subscriptionParameters->setSubscriptionItems($subscriptionItems);
            $subscriptionService->send($subscriptionParameters);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(3, self::$mqttClient));
            if ($callbackException !== null) {
                throw($callbackException);
            }
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