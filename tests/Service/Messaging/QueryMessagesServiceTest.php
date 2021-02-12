<?php

namespace Lib\Tests\Service\Messaging {

    use Agrirouter\Commons\Message;
    use Agrirouter\Commons\Messages;
    use Agrirouter\Feed\Request\ValidityPeriod;
    use Agrirouter\Feed\Response\HeaderQueryResponse;
    use App\Api\Exceptions\DecodeMessageException;
    use App\Api\Exceptions\OutboxException;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\HttpMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\Http\OutboxService;
    use App\Service\Messaging\QueryMessagesService;
    use App\Service\Parameters\QueryMessagesParameters;
    use Exception;
    use Google\Protobuf\Timestamp;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;

    class QueryMessagesServiceTest extends AbstractIntegrationTestForServices
    {

        /**
         * @covers QueryMessagesService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         * @throws Exception
         */
        function testGivenMissingFilterCriteriaForMessageQueryWhenSendingQueryMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $queryHeadersService = new QueryMessagesService($httpMessagingService);

            $queryMessagesParameters = new QueryMessagesParameters();
            $queryMessagesParameters->setApplicationMessageId(UuidService::newUuid());
            $queryMessagesParameters->setApplicationMessageSeqNo(1);
            $queryMessagesParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));

            $messagingResult = $queryHeadersService->send($queryMessagesParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            SleepTimer::letTheAgrirouterProcessTheMessage();

            $outboxService = new OutboxService($guzzleHttpClientBuilder->build());
            $outboxResponse = $outboxService->fetch(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            self::assertEquals(200, $outboxResponse->getStatusCode());

            $messages = $outboxResponse->getMessages();
            self::assertCount(1, $messages);
            self::assertNotNull($messages[0]->getCommand());
            self::assertNotNull($messages[0]->getCommand()->getMessage());

            $decodeMessagesService = new DecodeMessageService();
            $decodedMessages = $decodeMessagesService->decodeResponse($messages[0]->getCommand()->getMessage());
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
        }

        /**
         * @covers QueryMessagesService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         * @throws Exception
         */
        function testGivenInvalidMessageIdForMessageQueryWhenSendingQueryMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $queryHeadersService = new QueryMessagesService($httpMessagingService);

            $queryMessagesParameters = new QueryMessagesParameters();
            $queryMessagesParameters->setApplicationMessageId(UuidService::newUuid());
            $queryMessagesParameters->setApplicationMessageSeqNo(1);
            $queryMessagesParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $queryMessagesParameters->setMessageIds([UuidService::newUuid()]);

            $messagingResult = $queryHeadersService->send($queryMessagesParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            SleepTimer::letTheAgrirouterProcessTheMessage();

            $outboxService = new OutboxService($guzzleHttpClientBuilder->build());
            $outboxResponse = $outboxService->fetch(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            self::assertEquals(200, $outboxResponse->getStatusCode());

            $messages = $outboxResponse->getMessages();
            self::assertCount(1, $messages);
            self::assertNotNull($messages[0]->getCommand());
            self::assertNotNull($messages[0]->getCommand()->getMessage());

            $decodeMessagesService = new DecodeMessageService();
            $decodedMessages = $decodeMessagesService->decodeResponse($messages[0]->getCommand()->getMessage());
            self::assertNotNull($decodedMessages);
            self::assertEquals(204, $decodedMessages->getResponseEnvelope()->getResponseCode());

            /** @var HeaderQueryResponse $decodedDetails */
            $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
            self::assertNotNull($decodedDetails);

            $queryMetrics = $decodedDetails->getQueryMetrics();
            self::assertEquals(0, $queryMetrics->getTotalMessagesInQuery());
        }

        /**
         * @covers QueryMessagesService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         * @throws Exception
         */
        function testGivenInvalidSenderIdForMessageQueryWhenSendingQueryMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $queryHeadersService = new QueryMessagesService($httpMessagingService);

            $queryMessagesParameters = new QueryMessagesParameters();
            $queryMessagesParameters->setApplicationMessageId(UuidService::newUuid());
            $queryMessagesParameters->setApplicationMessageSeqNo(1);
            $queryMessagesParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $queryMessagesParameters->setSenders([UuidService::newUuid()]);

            $messagingResult = $queryHeadersService->send($queryMessagesParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            SleepTimer::letTheAgrirouterProcessTheMessage();

            $outboxService = new OutboxService($guzzleHttpClientBuilder->build());
            $outboxResponse = $outboxService->fetch(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            self::assertEquals(200, $outboxResponse->getStatusCode());

            $messages = $outboxResponse->getMessages();
            self::assertCount(1, $messages);
            self::assertNotNull($messages[0]->getCommand());
            self::assertNotNull($messages[0]->getCommand()->getMessage());

            $decodeMessagesService = new DecodeMessageService();
            $decodedMessages = $decodeMessagesService->decodeResponse($messages[0]->getCommand()->getMessage());
            self::assertNotNull($decodedMessages);
            self::assertEquals(204, $decodedMessages->getResponseEnvelope()->getResponseCode());

            /** @var HeaderQueryResponse $decodedDetails */
            $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
            self::assertNotNull($decodedDetails);

            $queryMetrics = $decodedDetails->getQueryMetrics();
            self::assertEquals(0, $queryMetrics->getTotalMessagesInQuery());
        }

        /**
         * @covers QueryMessagesService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         * @throws Exception
         */
        function testGivenValidityPeriodAndMissingMessagesForMessageQueryWhenSendingQueryMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $queryHeadersService = new QueryMessagesService($httpMessagingService);

            $queryMessagesParameters = new QueryMessagesParameters();
            $queryMessagesParameters->setApplicationMessageId(UuidService::newUuid());
            $queryMessagesParameters->setApplicationMessageSeqNo(1);
            $queryMessagesParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $validityPeriod = new ValidityPeriod();
            $sentFrom = new Timestamp();
            $sentTo = new Timestamp();
            $validityPeriod->setSentFrom($sentFrom);
            $validityPeriod->setSentTo($sentTo);
            $queryMessagesParameters->setValidityPeriod($validityPeriod);

            $messagingResult = $queryHeadersService->send($queryMessagesParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            SleepTimer::letTheAgrirouterProcessTheMessage();

            $outboxService = new OutboxService($guzzleHttpClientBuilder->build());
            $outboxResponse = $outboxService->fetch(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            self::assertEquals(200, $outboxResponse->getStatusCode());

            $messages = $outboxResponse->getMessages();
            self::assertCount(1, $messages);
            self::assertNotNull($messages[0]->getCommand());
            self::assertNotNull($messages[0]->getCommand()->getMessage());

            $decodeMessagesService = new DecodeMessageService();
            $decodedMessages = $decodeMessagesService->decodeResponse($messages[0]->getCommand()->getMessage());
            self::assertNotNull($decodedMessages);
            self::assertEquals(204, $decodedMessages->getResponseEnvelope()->getResponseCode());

            /** @var HeaderQueryResponse $decodedDetails */
            $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
            self::assertNotNull($decodedDetails);

            $queryMetrics = $decodedDetails->getQueryMetrics();
            self::assertEquals(0, $queryMetrics->getTotalMessagesInQuery());
        }

    }
}