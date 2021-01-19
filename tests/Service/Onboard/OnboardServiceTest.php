<?php

namespace Lib\Tests\Service\Onboard {

    use App\Definitions\ApplicationTypeDefinitions;
    use App\Definitions\CertificationTypeDefinitions;
    use App\Definitions\GatewayTypeDefinitions;
    use App\Service\Common\UuidService;
    use App\Service\Onboard\OnboardService;
    use App\Service\OnboardParameters;
    use Lib\Tests\Applications\CommunicationUnit;
    use PHPUnit\Framework\TestCase;

    /**
     * Test.
     * @package Lib\Tests\Service\Onboard
     */
    class OnboardServiceTest extends TestCase
    {

        function testGivenInvalidRequestTokenWhenOnboardingThenThereShouldBeAnException()
        {
            $onboardService = new OnboardService();
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(CommunicationUnit::applicationId());
            $onboardingParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::pem());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::http());
            $onboardingParameters->setRegistrationCode("INVALID");
            $onboardingResponse = $onboardService->onboard($onboardingParameters);
            self::assertNotNull($onboardingResponse);
        }

    }
}