<?php

namespace Lib\Tests\Service\Messaging {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Direction;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\PushNotification;
    use App\Api\Builder\CapabilityBuilder;
    use App\Service\Common\HttpMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\CapabilitiesService;
    use App\Service\Parameters\CapabilityParameters;
    use Lib\Tests\Applications\CommunicationUnit;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;

    class CapabilitiesServiceTest extends AbstractIntegrationTestForServices
    {

        function testGivenEmptyCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilitiesService($httpMessagingService);

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
            $capabilitiesService = new CapabilitiesService($httpMessagingService);

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
            $capabilitiesService = new CapabilitiesService($httpMessagingService);

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
            $capabilitiesService = new CapabilitiesService($httpMessagingService);

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
            $capabilitiesService = new CapabilitiesService($httpMessagingService);

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
            $capabilitiesService = new CapabilitiesService($httpMessagingService);

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
            $capabilitiesService = new CapabilitiesService($httpMessagingService);

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
            $capabilitiesService = new CapabilitiesService($httpMessagingService);

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
            $capabilitiesService = new CapabilitiesService($httpMessagingService);

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
            $capabilitiesService = new CapabilitiesService($httpMessagingService);

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