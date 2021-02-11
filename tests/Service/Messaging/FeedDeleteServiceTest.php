<?php

namespace Lib\Tests\Service\Messaging {

    use Agrirouter\Commons\Message;
    use Agrirouter\Commons\Messages;
    use Agrirouter\Feed\Request\ValidityPeriod;
    use App\Api\Exceptions\DecodeMessageException;
    use App\Api\Exceptions\OutboxException;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\HttpMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\FeedDeleteService;
    use App\Service\Messaging\Http\OutboxService;
    use App\Service\Parameters\FeedDeleteParameters;
    use Google\Protobuf\Timestamp;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;

    class FeedDeleteServiceTest extends AbstractIntegrationTestForServices
    {

        /**
         * @covers FeedDeleteService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         */
        function testGivenMissingFilterCriteriaForDeletionWhenSendingDeleteMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $feedDeleteService = new FeedDeleteService($httpMessagingService);

            $feedDeleteParameters = new FeedDeleteParameters();
            $feedDeleteParameters->setApplicationMessageId(UuidService::newUuid());
            $feedDeleteParameters->setApplicationMessageSeqNo(1);
            $feedDeleteParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));

            $messagingResult = $feedDeleteService->send($feedDeleteParameters);

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
                self::assertEquals("VAL_000018", $message->getMessageCode());
                self::assertEquals("Information required to process message is missing or malformed. Query does not contain any filtering criteria: messageIds, senders or validityPeriod", $message->getMessage());
            }
        }

        /**
         * @covers FeedDeleteService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         */
        function testGivenInvalidMessageIdForDeletionWhenSendingDeleteMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $feedDeleteService = new FeedDeleteService($httpMessagingService);

            $feedDeleteParameters = new FeedDeleteParameters();
            $feedDeleteParameters->setApplicationMessageId(UuidService::newUuid());
            $feedDeleteParameters->setApplicationMessageSeqNo(1);
            $feedDeleteParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $feedDeleteParameters->setMessageIds([UuidService::newUuid()]);

            $messagingResult = $feedDeleteService->send($feedDeleteParameters);

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
        }

        /**
         * @covers FeedDeleteService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         */
        function testGivenInvalidSenderIdForDeletionWhenSendingDeleteMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $feedDeleteService = new FeedDeleteService($httpMessagingService);

            $feedDeleteParameters = new FeedDeleteParameters();
            $feedDeleteParameters->setApplicationMessageId(UuidService::newUuid());
            $feedDeleteParameters->setApplicationMessageSeqNo(1);
            $feedDeleteParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $feedDeleteParameters->setSenders([UuidService::newUuid()]);

            $messagingResult = $feedDeleteService->send($feedDeleteParameters);

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
        }

        /**
         * @covers FeedDeleteService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         */
        function testGivenValidityPeriodAndMissingMessagesForDeletionWhenSendingDeleteMessageRequestThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $feedDeleteService = new FeedDeleteService($httpMessagingService);

            $feedDeleteParameters = new FeedDeleteParameters();
            $feedDeleteParameters->setApplicationMessageId(UuidService::newUuid());
            $feedDeleteParameters->setApplicationMessageSeqNo(1);
            $feedDeleteParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $validityPeriod = new ValidityPeriod();
            $sentFrom = new Timestamp();
            $sentTo = new Timestamp();
            $validityPeriod->setSentFrom($sentFrom);
            $validityPeriod->setSentTo($sentTo);
            $feedDeleteParameters->setValidityPeriod($validityPeriod);

            $messagingResult = $feedDeleteService->send($feedDeleteParameters);

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
        }

    }
}