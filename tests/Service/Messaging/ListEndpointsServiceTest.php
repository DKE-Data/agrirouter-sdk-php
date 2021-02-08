<?php

namespace Lib\Tests\Service\Messaging {

    use Agrirouter\Request\Payload\Account\ListEndpointsQuery\Direction;
    use Agrirouter\Response\Payload\Account\ListEndpointsResponse;
    use App\Api\Exceptions\DecodeMessageException;
    use App\Api\Exceptions\OutboxException;
    use App\Definitions\CapabilityTypeDefinitions;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\HttpMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\Http\OutboxService;
    use App\Service\Messaging\ListEndpointsService;
    use App\Service\Parameters\ListEndpointsParameters;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;

    class ListEndpointsServiceTest extends AbstractIntegrationTestForServices
    {

        /**
         * @covers ListEndpointsService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         */
        function testGivenValidQueryWhenSendingListEndpointsMessageThenTheAgrirouterShouldAcceptTheMessageAndReturnTheQueryResult()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $listEndpointsService = new ListEndpointsService($httpMessagingService);

            $listEndpointsParameters = new ListEndpointsParameters();
            $listEndpointsParameters->setApplicationMessageId(UuidService::newUuid());
            $listEndpointsParameters->setApplicationMessageSeqNo(1);
            $listEndpointsParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $listEndpointsParameters->setTechnicalMessageType(CapabilityTypeDefinitions::ISO_11783_TASKDATA_ZIP);
            $listEndpointsParameters->setDirection(Direction::SEND_RECEIVE);

            $messagingResult = $listEndpointsService->send($listEndpointsParameters);

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
            self::assertEquals(200, $decodedMessages->getResponseEnvelope()->getResponseCode());

            /** @var ListEndpointsResponse $decodedDetails */
            $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
            self::assertNotNull($decodedDetails);

            $endpoints = $decodedDetails->getEndpoints();
            self::assertGreaterThan(0, $endpoints->count());

            $iterator = $endpoints->getIterator();
            /** @var ListEndpointsResponse\Endpoint $endpoint */
            foreach ($iterator as $endpoint) {
                self::assertNotNull($endpoint);
                self::assertNotNull($endpoint->getExternalId());
                self::assertNotNull($endpoint->getEndpointId());
                self::assertNotNull($endpoint->getEndpointName());
                self::assertNotNull($endpoint->getEndpointType());
                self::assertNotNull($endpoint->getMessageTypes());
                self::assertNotNull($endpoint->getStatus());
            }
        }

        /**
         * @covers ListEndpointsService::send()
         * @throws DecodeMessageException
         * @throws OutboxException
         */
        function testGivenInvalidQueryWhenSendingListEndpointsFilteredMessageThenTheAgrirouterShouldAcceptTheMessageAndReturnAnErrorMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $listEndpointsService = new ListEndpointsService($httpMessagingService);

            $listEndpointsParameters = new ListEndpointsParameters();
            $listEndpointsParameters->setApplicationMessageId(UuidService::newUuid());
            $listEndpointsParameters->setApplicationMessageSeqNo(1);
            $listEndpointsParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $listEndpointsParameters->setTechnicalMessageType(CapabilityTypeDefinitions::ISO_11783_TASKDATA_ZIP);
            $listEndpointsParameters->setDirection(Direction::SEND_RECEIVE);
            $listEndpointsParameters->setFiltered(true);

            $messagingResult = $listEndpointsService->send($listEndpointsParameters);

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
            self::assertEquals(200, $decodedMessages->getResponseEnvelope()->getResponseCode());

            /** @var ListEndpointsResponse $decodedDetails */
            $decodedDetails = $decodeMessagesService->decodeDetails($decodedMessages->getResponsePayloadWrapper()->getDetails());
            self::assertNotNull($decodedDetails);

            $endpoints = $decodedDetails->getEndpoints();
            self::assertGreaterThan(0, $endpoints->count());

            $iterator = $endpoints->getIterator();
            /** @var ListEndpointsResponse\Endpoint $endpoint */
            foreach ($iterator as $endpoint) {
                self::assertNotNull($endpoint);
                self::assertNotNull($endpoint->getExternalId());
                self::assertNotNull($endpoint->getEndpointId());
                self::assertNotNull($endpoint->getEndpointName());
                self::assertNotNull($endpoint->getEndpointType());
                self::assertNotNull($endpoint->getMessageTypes());
                self::assertNotNull($endpoint->getStatus());
            }
        }

    }
}