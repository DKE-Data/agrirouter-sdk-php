<?php

namespace Lib\Tests\Service\Messaging\Mqtt\Cloud {

    use Agrirouter\Cloud\Registration\OnboardingRequest\EndpointRegistrationDetails;
    use Agrirouter\Commons\Message;
    use Agrirouter\Commons\Messages;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Cloud\CloudOnboardService;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\MqttMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Parameters\CloudOnboardParameters;
    use Exception;
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

    class CloudOnboardServiceTest extends AbstractIntegrationTestForServices
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
         * @covers CloudOnboardService::send()
         * @throws Exception
         */
        function testGivenCommunicationUnitWhenSendingCloudOnboardRequestThenTheAgrirouterShouldAcceptTheMessageButReturnAnAckWithMessage()
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
                            self::assertEquals("The application is not a cloud application.", $message->getMessage());
                        }

                    } catch (Exception $exception) {
                        $callbackException = $exception;
                    }
                    $logger->info("Leaving callback...");
                    $mqttClient->interrupt();
                });

            $cloudOnboardService = new CloudOnboardService(new MqttMessagingService(self::$mqttClient));

            $cloudOnboardParameters = new CloudOnboardParameters();
            $cloudOnboardParameters->setApplicationMessageId(UuidService::newUuid());
            $cloudOnboardParameters->setApplicationMessageSeqNo(1);
            $cloudOnboardParameters->setOnboardResponse(self::$onboardResponse);

            $endpointRegistrationDetails = new EndpointRegistrationDetails();
            $endpointRegistrationDetails->setId(UuidService::newUuid());
            $endpointRegistrationDetails->setName("VCU | INTEGRATION-TEST | SDK for PHP");
            $onboardRequests = [];
            array_push($onboardRequests, $endpointRegistrationDetails);
            $cloudOnboardParameters->setOnboardingRequests($onboardRequests);

            $cloudOnboardService->send($cloudOnboardParameters);

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