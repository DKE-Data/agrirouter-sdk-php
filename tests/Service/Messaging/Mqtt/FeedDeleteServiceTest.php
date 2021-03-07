<?php

namespace Lib\Tests\Service\Messaging\Mqtt {

    use Agrirouter\Commons\Message;
    use Agrirouter\Commons\Messages;
    use Agrirouter\Feed\Request\ValidityPeriod;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\MqttMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\FeedDeleteService;
    use App\Service\Parameters\FeedDeleteParameters;
    use Exception;
    use Google\Protobuf\Timestamp;
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
    use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
    use PhpMqtt\Client\Exceptions\RepositoryException;

    class FeedDeleteServiceTest extends AbstractIntegrationTestForServices
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
         * @covers FeedDeleteService::send()
         * @throws Exception
         */
        function testGivenMissingFilterCriteriaForDeletionWhenSendingDeleteMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
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
                            self::assertEquals("VAL_000018", $message->getMessageCode());
                            self::assertEquals("Information required to process message is missing or malformed. Query does not contain any filtering criteria: messageIds, senders or validityPeriod", $message->getMessage());
                        }
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });
            $feedDeleteService = new FeedDeleteService(new MqttMessagingService(self::$mqttClient));
            $feedDeleteParameters = new FeedDeleteParameters();
            $feedDeleteParameters->setApplicationMessageId(UuidService::newUuid());
            $feedDeleteParameters->setApplicationMessageSeqNo(1);
            $feedDeleteParameters->setOnboardResponse(self::$onboardResponse);

            $feedDeleteService->send($feedDeleteParameters);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
            if ($callbackException !== null) {
                throw($callbackException);
            }
        }

        /**
         * @covers FeedDeleteService::send()
         * @throws Exception
         */
        function testGivenInvalidMessageIdForDeletionWhenSendingDeleteMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
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
                        self::assertEquals(204, $decodedMessages->getResponseEnvelope()->getResponseCode());
                        /** @var Messages $decodedDetails */
                        $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
                        self::assertNotNull($decodedDetails);
                        $agrirouterMessages = $decodedDetails->getMessages();
                        self::assertEquals(1, $agrirouterMessages->count());
                        $iterator = $agrirouterMessages->getIterator();
                        /** @var Message $message */
                        foreach ($iterator as $message) {
                            self::assertEquals("VAL_000208", $message->getMessageCode());
                            self::assertEquals("Feed does not contain any data to be deleted.", $message->getMessage());
                        }
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });
            $feedDeleteService = new FeedDeleteService(new MqttMessagingService(self::$mqttClient));
            $feedDeleteParameters = new FeedDeleteParameters();
            $feedDeleteParameters->setApplicationMessageId(UuidService::newUuid());
            $feedDeleteParameters->setApplicationMessageSeqNo(1);
            $feedDeleteParameters->setOnboardResponse(self::$onboardResponse);
            $feedDeleteParameters->setMessageIds([UuidService::newUuid()]);
            $feedDeleteService->send($feedDeleteParameters);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
            if ($callbackException !== null) {
                throw($callbackException);
            }
        }

        /**
         * @covers FeedDeleteService::send()
         * @throws Exception
         */
        function testGivenInvalidSenderIdForDeletionWhenSendingDeleteMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
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
                        self::assertEquals(204, $decodedMessages->getResponseEnvelope()->getResponseCode());
                        /** @var Messages $decodedDetails */
                        $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
                        self::assertNotNull($decodedDetails);
                        $agrirouterMessages = $decodedDetails->getMessages();
                        self::assertEquals(1, $agrirouterMessages->count());
                        $iterator = $agrirouterMessages->getIterator();
                        /** @var Message $message */
                        foreach ($iterator as $message) {
                            self::assertEquals("VAL_000208", $message->getMessageCode());
                            self::assertEquals("Feed does not contain any data to be deleted.", $message->getMessage());
                        }
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });
            $feedDeleteService = new FeedDeleteService(new MqttMessagingService(self::$mqttClient));
            $feedDeleteParameters = new FeedDeleteParameters();
            $feedDeleteParameters->setApplicationMessageId(UuidService::newUuid());
            $feedDeleteParameters->setApplicationMessageSeqNo(1);
            $feedDeleteParameters->setOnboardResponse(self::$onboardResponse);
            $feedDeleteParameters->setSenders([UuidService::newUuid()]);
            $feedDeleteService->send($feedDeleteParameters);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
            if ($callbackException !== null) {
                throw($callbackException);
            }
        }

        /**
         * @covers FeedDeleteService::send()
         * @throws Exception
         */
        function testGivenValidityPeriodAndMissingMessagesForDeletionWhenSendingDeleteMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
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
                        self::assertEquals(204, $decodedMessages->getResponseEnvelope()->getResponseCode());
                        /** @var Messages $decodedDetails */
                        $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
                        self::assertNotNull($decodedDetails);
                        $agrirouterMessages = $decodedDetails->getMessages();
                        self::assertEquals(1, $agrirouterMessages->count());
                        $iterator = $agrirouterMessages->getIterator();
                        /** @var Message $message */
                        foreach ($iterator as $message) {
                            self::assertEquals("VAL_000208", $message->getMessageCode());
                            self::assertEquals("Feed does not contain any data to be deleted.", $message->getMessage());
                        }
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });
            $feedDeleteService = new FeedDeleteService(new MqttMessagingService(self::$mqttClient));
            $feedDeleteParameters = new FeedDeleteParameters();
            $feedDeleteParameters->setApplicationMessageId(UuidService::newUuid());
            $feedDeleteParameters->setApplicationMessageSeqNo(1);
            $feedDeleteParameters->setOnboardResponse(self::$onboardResponse);
            $validityPeriod = new ValidityPeriod();
            $sentFrom = new Timestamp();
            $sentTo = new Timestamp();
            $validityPeriod->setSentFrom($sentFrom);
            $validityPeriod->setSentTo($sentTo);
            $feedDeleteParameters->setValidityPeriod($validityPeriod);
            $feedDeleteService->send($feedDeleteParameters);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(mqttClient: self::$mqttClient));
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