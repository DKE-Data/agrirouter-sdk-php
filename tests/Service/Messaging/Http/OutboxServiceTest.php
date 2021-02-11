<?php

namespace Lib\Tests\Service\Messaging\Http {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Capability;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Direction;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\PushNotification;
    use App\Api\Exceptions\OutboxException;
    use App\Service\Common\HttpMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\CapabilityService;
    use App\Service\Messaging\Http\OutboxService;
    use App\Service\Parameters\CapabilityParameters;
    use Exception;
    use Lib\Tests\Applications\CommunicationUnit;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;
    use Lib\Tests\Service\Common\SleepTimer;

    class OutboxServiceTest extends AbstractIntegrationTestForServices
    {

        /**
         * @covers OutboxService::fetch()
         * @throws OutboxException
         * @throws Exception
         */
        function testGivenInvalidCapabilitiesWhenSendingCapabilitiesThenTheAgrirouterShouldStillAcceptTheMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);
            $outboxService = new OutboxService($guzzleHttpClientBuilder->build());

            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $capabilityParameters->setEnablePushNotification(PushNotification::DISABLED);

            $capability = new Capability();
            $capability->setDirection(Direction::SEND_RECEIVE);
            $capability->setTechnicalMessageType("That one is invalid!");
            $capabilities = [];
            array_push($capabilities, $capability);
            $capabilityParameters->setCapabilityParameters($capabilities);

            $messagingResult = $capabilitiesService->send($capabilityParameters);

            SleepTimer::letTheAgrirouterProcessTheMessage();

            self::assertNotEmpty($messagingResult);
            self::assertNotEmpty($messagingResult->getMessageIds());
            self::assertCount(1, $messagingResult->getMessageIds());

            $response = $outboxService->fetch(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            self::assertEquals(200, $response->getStatusCode());
            $messages = $response->getMessages();
            self::assertCount(1, $messages);
            self::assertEquals("1c04474f-53ab-4e87-8feb-71b3dc3b86df", $messages[0]->getSensorAlternateId());
            self::assertEquals("0a6f749d-7e55-49c3-98f4-77a6335ac2b7", $messages[0]->getCapabilityAlternateId());
            self::assertNotNull($messages[0]->getCommand());
            self::assertNotNull($messages[0]->getCommand()->getMessage());
        }

    }
}