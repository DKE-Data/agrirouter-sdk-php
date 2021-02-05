<?php

namespace Lib\Tests\Service\Messaging {

    use Agrirouter\Commons\Message;
    use Agrirouter\Commons\Messages;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Capability;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Direction;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\PushNotification;
    use App\Api\Builder\CapabilityBuilder;
    use App\Service\Common\DecodeMessageService;
    use App\Service\Common\HttpMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\CapabilityService;
    use App\Service\Messaging\Http\OutboxService;
    use App\Service\Parameters\CapabilityParameters;
    use Lib\Tests\Applications\CommunicationUnit;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;

    class CapabilityServiceTest extends AbstractIntegrationTestForServices
    {

        function testGivenInvalidCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldStillAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capability = new Capability();
            $capability->setDirection(Direction::SEND_RECEIVE);
            $capability->setTechnicalMessageType("That one is invalid!");
            $capabilities = [];
            array_push($capabilities, $capability);
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

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
                self::assertEquals("Capability for That one is invalid! was ignored as it is not known to the certification.", $message->getMessage());
            }
        }

        function testGivenEmptyCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilityParameters->setCapabilityParameters($capabilityBuilder->build());

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());
        }

        function testGivenTaskdataCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTaskdata(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTaskdata(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTaskdata(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());
        }

        function testGivenDeviceDescriptionCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withDeviceDescription(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withDeviceDescription(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withDeviceDescription(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());
        }

        function testGivenTimeLogCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTimeLog(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTimeLog(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withTimeLog(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());
        }

        function testGivenImageCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withBmp(Direction::SEND)
                ->withJpg(Direction::SEND)
                ->withPng(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withBmp(Direction::RECEIVE)
                ->withJpg(Direction::RECEIVE)
                ->withPng(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withBmp(Direction::SEND_RECEIVE)
                ->withJpg(Direction::SEND_RECEIVE)
                ->withPng(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());
        }

        function testGivenVideoCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withAvi(Direction::SEND)
                ->withMp4(Direction::SEND)
                ->withWmv(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withAvi(Direction::RECEIVE)
                ->withMp4(Direction::RECEIVE)
                ->withWmv(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withAvi(Direction::SEND_RECEIVE)
                ->withMp4(Direction::SEND_RECEIVE)
                ->withWmv(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());
        }

        function testGivenShapeCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withShape(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withShape(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withShape(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());
        }

        function testGivenPdfCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withPdf(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withPdf(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withPdf(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());
        }

        function testGivenGpsInfoCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withGpsInfo(Direction::SEND)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withGpsInfo(Direction::RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder->withGpsInfo(Direction::SEND_RECEIVE)->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());
        }

        function testGivenAllCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder
                ->withTaskdata(Direction::SEND)
                ->withDeviceDescription(Direction::SEND)
                ->withTimeLog(Direction::SEND)
                ->withBmp(Direction::SEND)
                ->withJpg(Direction::SEND)
                ->withPng(Direction::SEND)
                ->withShape(Direction::SEND)
                ->withPdf(Direction::SEND)
                ->withAvi(Direction::SEND)
                ->withMp4(Direction::SEND)
                ->withWmv(Direction::SEND)
                ->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder
                ->withTaskdata(Direction::RECEIVE)
                ->withDeviceDescription(Direction::RECEIVE)
                ->withTimeLog(Direction::RECEIVE)
                ->withBmp(Direction::RECEIVE)
                ->withJpg(Direction::RECEIVE)
                ->withPng(Direction::RECEIVE)
                ->withShape(Direction::RECEIVE)
                ->withPdf(Direction::RECEIVE)
                ->withAvi(Direction::RECEIVE)
                ->withMp4(Direction::RECEIVE)
                ->withWmv(Direction::RECEIVE)
                ->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $capabilityBuilder = new CapabilityBuilder();
            $capabilities = $capabilityBuilder
                ->withTaskdata(Direction::SEND_RECEIVE)
                ->withDeviceDescription(Direction::SEND_RECEIVE)
                ->withTimeLog(Direction::SEND_RECEIVE)
                ->withBmp(Direction::SEND_RECEIVE)
                ->withJpg(Direction::SEND_RECEIVE)
                ->withPng(Direction::SEND_RECEIVE)
                ->withShape(Direction::SEND_RECEIVE)
                ->withPdf(Direction::SEND_RECEIVE)
                ->withAvi(Direction::SEND_RECEIVE)
                ->withMp4(Direction::SEND_RECEIVE)
                ->withWmv(Direction::SEND_RECEIVE)
                ->build();
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());
        }

    }
}