<?php

namespace Lib\Tests\Service\Messaging {

    use Agrirouter\Commons\Message;
    use Agrirouter\Commons\Messages;
    use Agrirouter\Request\Payload\Endpoint\Subscription\MessageTypeSubscriptionItem;
    use App\Definitions\CapabilityTypeDefinitions;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\HttpMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\Http\OutboxService;
    use App\Service\Messaging\SubscriptionService;
    use App\Service\Parameters\SubscriptionParameters;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;

    class SubscriptionServiceTest extends AbstractIntegrationTestForServices
    {

        function testGivenInvalidSubscriptionWhenSendingSubscriptionThenTheAgrirouterShouldStillAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $subscriptionService = new SubscriptionService($httpMessagingService);

            $subscriptionParameters = new SubscriptionParameters();
            $subscriptionParameters->setApplicationMessageId(UuidService::newUuid());
            $subscriptionParameters->setApplicationMessageSeqNo(1);
            $subscriptionParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));

            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType("This one is invalid.");
            $subscriptionItems = [];
            array_push($subscriptionItems, $subscriptionItem);
            $subscriptionParameters->setSubscriptionItems($subscriptionItems);

            $messagingResult = $subscriptionService->send($subscriptionParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            SleepTimer::letTheAgrirouterProcessTheMessage();

            $outboxService = new OutboxService($guzzleHttpClientBuilder->build());
            $outboxResponse = $outboxService->fetch(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
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
                self::assertEquals("Subscription to \"This one is invalid.\" is not valid per reported capabilities.", $message->getMessage());
            }
        }

        function testGivenValidSubscriptionWhenSendingSubscriptionThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $subscriptionService = new SubscriptionService($httpMessagingService);

            $subscriptionParameters = new SubscriptionParameters();
            $subscriptionParameters->setApplicationMessageId(UuidService::newUuid());
            $subscriptionParameters->setApplicationMessageSeqNo(1);
            $subscriptionParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));

            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::ISO_11783_TASKDATA_ZIP);
            $subscriptionItems = [];
            array_push($subscriptionItems, $subscriptionItem);
            $subscriptionParameters->setSubscriptionItems($subscriptionItems);

            $messagingResult = $subscriptionService->send($subscriptionParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            SleepTimer::letTheAgrirouterProcessTheMessage();

            $outboxService = new OutboxService($guzzleHttpClientBuilder->build());
            $outboxResponse = $outboxService->fetch(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            self::assertEquals(200, $outboxResponse->getStatusCode());

            $messages = $outboxResponse->getMessages();
            self::assertCount(1, $messages);
            self::assertNotNull($messages[0]->getCommand());
            self::assertNotNull($messages[0]->getCommand()->getMessage());

            $decodeMessagesService = new DecodeMessageService();
            $decodedMessages = $decodeMessagesService->decodeResponse($messages[0]->getCommand()->getMessage());
            self::assertNotNull($decodedMessages);
            self::assertEquals(201, $decodedMessages->getResponseEnvelope()->getResponseCode());
        }

    }
}