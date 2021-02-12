<?php

namespace Lib\Tests\Service\Messaging\Http\Cloud {

    use Agrirouter\Cloud\Registration\OnboardingRequest\EndpointRegistrationDetails;
    use Agrirouter\Commons\Message;
    use Agrirouter\Commons\Messages;
    use App\Api\Exceptions\DecodeMessageException;
    use App\Api\Exceptions\OutboxException;
    use App\Service\Cloud\CloudOnboardService;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\HttpMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\Http\OutboxService;
    use App\Service\Parameters\CloudOnboardParameters;
    use Exception;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;

    class CloudOnboardServiceTest extends AbstractIntegrationTestForServices
    {

        /**
         * @covers CloudOnboardService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         * @throws Exception
         */
        function testGivenCommunicationUnitWhenSendingCloudOnboardRequestThenTheAgrirouterShouldAcceptTheMessageButReturnAnAckWithMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $cloudOnboardService = new CloudOnboardService($httpMessagingService);

            $cloudOnboardParameters = new CloudOnboardParameters();
            $cloudOnboardParameters->setApplicationMessageId(UuidService::newUuid());
            $cloudOnboardParameters->setApplicationMessageSeqNo(1);
            $cloudOnboardParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));

            $endpointRegistrationDetails = new EndpointRegistrationDetails();
            $endpointRegistrationDetails->setId(UuidService::newUuid());
            $endpointRegistrationDetails->setName("VCU | INTEGRATION-TEST | SDK for PHP");
            $onboardRequests = [];
            array_push($onboardRequests, $endpointRegistrationDetails);
            $cloudOnboardParameters->setOnboardingRequests($onboardRequests);

            $messagingResult = $cloudOnboardService->send($cloudOnboardParameters);

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
                self::assertEquals("The application is not a cloud application.", $message->getMessage());
            }
        }

    }
}