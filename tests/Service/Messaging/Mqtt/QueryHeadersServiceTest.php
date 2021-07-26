<?php

namespace Lib\Tests\Service\Messaging\Mqtt {

    use Agrirouter\Commons\Message;
    use Agrirouter\Commons\Messages;
    use Agrirouter\Feed\Request\ValidityPeriod;
    use Agrirouter\Feed\Response\HeaderQueryResponse;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\MqttMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\QueryHeadersService;
    use App\Service\Parameters\QueryHeadersParameters;
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

    class QueryHeadersServiceTest extends AbstractIntegrationTestForServices
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
         * @covers QueryHeadersService::send()
         * @throws Exception
         */
        function testGivenMissingFilterCriteriaForHeaderQueryWhenSendingQueryHeadersMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
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
                            self::assertEquals("VAL_000017", $message->getMessageCode());
                            self::assertEquals("Query does not contain any filtering criteria: messageIds, senders or validityPeriod. Information required to process message is missing or malformed.", $message->getMessage());
                        }
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });
            $queryHeadersService = new QueryHeadersService(new MqttMessagingService(self::$mqttClient));
            $queryHeadersParameters = new QueryHeadersParameters();
            $queryHeadersParameters->setApplicationMessageId(UuidService::newUuid());
            $queryHeadersParameters->setApplicationMessageSeqNo(1);
            $queryHeadersParameters->setOnboardResponse(self::$onboardResponse);
            $queryHeadersService->send($queryHeadersParameters);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(3, self::$mqttClient));
            if ($callbackException !== null) {
                throw($callbackException);
            }
        }

        /**
         * @covers QueryHeadersService::send()
         * @throws Exception
         */
        function testGivenInvalidMessageIdForHeaderQueryWhenSendingQueryHeadersMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
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
                    $receivedMessage = json_decode($message, true);
                    try {
                        $decodeMessagesService = new DecodeMessageService();
                        $decodedMessages = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);
                        self::assertNotNull($decodedMessages);
                        self::assertEquals(204, $decodedMessages->getResponseEnvelope()->getResponseCode());
                        /** @var HeaderQueryResponse $decodedDetails */
                        $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
                        self::assertNotNull($decodedDetails);
                        $queryMetrics = $decodedDetails->getQueryMetrics();
                        self::assertEquals(0, $queryMetrics->getTotalMessagesInQuery());
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });
            $queryHeadersService = new QueryHeadersService(new MqttMessagingService(self::$mqttClient));
            $queryHeadersParameters = new QueryHeadersParameters();
            $queryHeadersParameters->setApplicationMessageId(UuidService::newUuid());
            $queryHeadersParameters->setApplicationMessageSeqNo(1);
            $queryHeadersParameters->setOnboardResponse(self::$onboardResponse);
            $queryHeadersParameters->setMessageIds([UuidService::newUuid()]);
            $queryHeadersService->send($queryHeadersParameters);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(3, self::$mqttClient));
            if ($callbackException !== null) {
                throw($callbackException);
            }
        }

        /**
         * @covers QueryHeadersService::send()
         * @throws Exception
         */
        function testGivenInvalidSenderIdForHeaderQueryWhenSendingQueryHeadersMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
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
                    $receivedMessage = json_decode($message, true);
                    try {
                        $decodeMessagesService = new DecodeMessageService();
                        $decodedMessages = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);
                        self::assertNotNull($decodedMessages);
                        self::assertEquals(204, $decodedMessages->getResponseEnvelope()->getResponseCode());
                        /** @var HeaderQueryResponse $decodedDetails */
                        $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
                        self::assertNotNull($decodedDetails);
                        $queryMetrics = $decodedDetails->getQueryMetrics();
                        self::assertEquals(0, $queryMetrics->getTotalMessagesInQuery());
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });
            $queryHeadersService = new QueryHeadersService(new MqttMessagingService(self::$mqttClient));
            $queryHeadersParameters = new QueryHeadersParameters();
            $queryHeadersParameters->setApplicationMessageId(UuidService::newUuid());
            $queryHeadersParameters->setApplicationMessageSeqNo(1);
            $queryHeadersParameters->setOnboardResponse(self::$onboardResponse);
            $queryHeadersParameters->setSenders([UuidService::newUuid()]);
            $queryHeadersService->send($queryHeadersParameters);

            self::assertTrue(SleepTimer::letTheAgrirouterProcessTheMqttMessage(3, self::$mqttClient));
            if ($callbackException !== null) {
                throw($callbackException);
            }
        }

        /**
         * @covers QueryHeadersService::send()
         * @throws Exception
         */
        function testGivenValidityPeriodAndMissingMessagesForHeaderQueryWhenSendingQueryHeadersMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
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
                    $receivedMessage = json_decode($message, true);
                    try {
                        $decodeMessagesService = new DecodeMessageService();
                        $decodedMessages = $decodeMessagesService->decodeResponse($receivedMessage['command']['message']);
                        self::assertNotNull($decodedMessages);
                        self::assertEquals(204, $decodedMessages->getResponseEnvelope()->getResponseCode());
                        /** @var HeaderQueryResponse $decodedDetails */
                        $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
                        self::assertNotNull($decodedDetails);
                        $queryMetrics = $decodedDetails->getQueryMetrics();
                        self::assertEquals(0, $queryMetrics->getTotalMessagesInQuery());
                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });
            $queryHeadersService = new QueryHeadersService(new MqttMessagingService(self::$mqttClient));
            $queryHeadersParameters = new QueryHeadersParameters();
            $queryHeadersParameters->setApplicationMessageId(UuidService::newUuid());
            $queryHeadersParameters->setApplicationMessageSeqNo(1);
            $queryHeadersParameters->setOnboardResponse(self::$onboardResponse);
            $validityPeriod = new ValidityPeriod();
            $sentFrom = new Timestamp();
            $sentTo = new Timestamp();
            $validityPeriod->setSentFrom($sentFrom);
            $validityPeriod->setSentTo($sentTo);
            $queryHeadersParameters->setValidityPeriod($validityPeriod);
            $queryHeadersService->send($queryHeadersParameters);

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