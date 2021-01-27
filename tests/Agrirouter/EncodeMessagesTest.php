<?php

namespace Lib\Tests\Agrirouter {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification;
    use App\Service\Common\HttpMessagingService;
    use App\Service\Common\UuidService;
    use App\Service\Messaging\CapabilitiesService;
    use App\Service\Parameters\CapabilityParameters;
    use Lib\Tests\Applications\CommunicationUnit;
    use Lib\Tests\Helper\HttpClientFactory;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use PHPUnit\Framework\TestCase;

    class EncodeMessagesTest extends TestCase
    {

        /**
         * @covers \App\Service\Messaging\CapabilitiesService::encode
         */
        public function testGivenCapabilityParametersWhenEncodingTheMessageThenTheCapabilitiesServiceShouldCreateAnEncodedMessage()
        {
            $capabilitiesService = new CapabilitiesService(new HttpMessagingService(HttpClientFactory::httpClient()));
            $capabilityParameters = new CapabilityParameters();
            $capabilityParameters->setApplicationMessageId(UuidService::newUuid());
            $capabilityParameters->setApplicationMessageSeqNo(1);
            $capabilityParameters->setApplicationId(CommunicationUnit::applicationId());
            $capabilityParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $capabilityParameters->setOnboardResponse(OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT));
            $capabilityParameters->setEnablePushNotification(CapabilitySpecification\PushNotification::DISABLED);
            $encodedMessage = $capabilitiesService->encode($capabilityParameters);
            self::assertNotEmpty($encodedMessage);
        }

    }
}