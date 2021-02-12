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
    use App\Service\Messaging\QueryHeadersService;
    use App\Service\Parameters\QueryHeadersParameters;
    use Exception;
    use Google\Protobuf\Timestamp;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;

    class QueryHeadersServiceTest extends AbstractIntegrationTestForServices
    {

        /**
         * @covers QueryHeadersService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         * @throws Exception
         */
        function testGivenMissingFilterCriteriaForHeaderQueryWhenSendingQueryHeadersMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $queryHeadersService = new QueryHeadersService($httpMessagingService);

            $queryHeadersParameters = new QueryHeadersParameters();
            $queryHeadersParameters->setApplicationMessageId(UuidService::newUuid());
            $queryHeadersParameters->setApplicationMessageSeqNo(1);
            $queryHeadersParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));

            $messagingResult = $queryHeadersService->send($queryHeadersParameters);

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
         * @covers QueryHeadersService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         * @throws Exception
         */
        function testGivenInvalidMessageIdForHeaderQueryWhenSendingQueryHeadersMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $queryHeadersService = new QueryHeadersService($httpMessagingService);

            $queryHeadersParameters = new QueryHeadersParameters();
            $queryHeadersParameters->setApplicationMessageId(UuidService::newUuid());
            $queryHeadersParameters->setApplicationMessageSeqNo(1);
            $queryHeadersParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $queryHeadersParameters->setMessageIds([UuidService::newUuid()]);

            $messagingResult = $queryHeadersService->send($queryHeadersParameters);

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
         * @covers QueryHeadersService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         * @throws Exception
         */
        function testGivenInvalidSenderIdForHeaderQueryWhenSendingQueryHeadersMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $queryHeadersService = new QueryHeadersService($httpMessagingService);

            $queryHeadersParameters = new QueryHeadersParameters();
            $queryHeadersParameters->setApplicationMessageId(UuidService::newUuid());
            $queryHeadersParameters->setApplicationMessageSeqNo(1);
            $queryHeadersParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $queryHeadersParameters->setSenders([UuidService::newUuid()]);

            $messagingResult = $queryHeadersService->send($queryHeadersParameters);

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
         * @covers QueryHeadersService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         * @throws Exception
         */
        function testGivenValidityPeriodAndMissingMessagesForHeaderQueryWhenSendingQueryHeadersMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $queryHeadersService = new QueryHeadersService($httpMessagingService);

            $queryHeadersParameters = new QueryHeadersParameters();
            $queryHeadersParameters->setApplicationMessageId(UuidService::newUuid());
            $queryHeadersParameters->setApplicationMessageSeqNo(1);
            $queryHeadersParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $validityPeriod = new ValidityPeriod();
            $sentFrom = new Timestamp();
            $sentTo = new Timestamp();
            $validityPeriod->setSentFrom($sentFrom);
            $validityPeriod->setSentTo($sentTo);
            $queryHeadersParameters->setValidityPeriod($validityPeriod);

            $messagingResult = $queryHeadersService->send($queryHeadersParameters);

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