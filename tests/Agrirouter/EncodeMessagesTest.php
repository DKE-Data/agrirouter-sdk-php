<?php

namespace Lib\Tests\Agrirouter {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification;
    use App\Service\Common\HttpMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\CapabilityService;
    use App\Service\Parameters\CapabilityParameters;
    use Lib\Tests\Applications\CommunicationUnit;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use PHPUnit\Framework\TestCase;

    class EncodeMessagesTest extends TestCase
    {

        /**
         * @covers \App\Service\Messaging\CapabilitiesServiceInterface::encode
         */
        public function testGivenCapabilityParametersWhenEncodingTheMessageThenTheCapabilitiesServiceShouldCreateAnEncodedMessage()
        {
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $capabilitiesService = new CapabilityService($httpMessagingService);
            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT_HTTP));
            $capabilityParameters->setEnablePushNotification(CapabilitySpecification\PushNotification::DISABLED);
            $encodedMessage = $capabilitiesService->encode($capabilityParameters);
            self::assertNotEmpty($encodedMessage);
        }

    }
}