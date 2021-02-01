<?php declare(strict_types=1);

namespace Lib\Tests\Service\Onboard {

    use App\Api\Exceptions\EndpointVerificationException;
    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OnboardException;
    use App\Definitions\ApplicationTypeDefinitions;
    use App\Definitions\CertificationTypeDefinitions;
    use App\Definitions\GatewayTypeDefinitions;
    use App\Environment\QualityAssuranceEnvironment;
    use App\Service\Common\UuidService;
    use App\Service\Onboard\AuthorizationService;
    use App\Service\Onboard\SecuredEndpointVerificationService;
    use App\Service\Onboard\SecuredOnboardService;
    use App\Service\Parameters\OnboardParameters;
    use DateTime;
    use DateTimeZone;
    use Lib\Tests\Applications\FarmingSoftware;
    use Lib\Tests\Applications\TelemetryPlatform;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;

    /**
     * Class TelemetryPlatformSecuredOnboardVerificationServiceTest
     * @package Lib\Tests\Service\Onboard
     */
    class TelemetryPlatformSecuredOnboardVerificationServiceTest extends AbstractIntegrationTestForServices
    {
        /**
         * @covers \App\Service\Onboard\SecuredOnboardService::verify
         * @throws OnboardException
         */
        public function testGivenInvalidRequestTokenWhenVerifyOnboardingTelemetryPlatformThenThereShouldBeAnException()
        {
            self::expectException(OnboardException::class);
            self::expectExceptionCode(ErrorCodes::BEARER_NOT_FOUND);

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $onboardService = new SecuredOnboardService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(TelemetryPlatform::applicationId());
            $onboardingParameters->setCertificationVersionId(TelemetryPlatform::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::p12());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::http());
            $onboardingParameters->setRegistrationCode("INVALID");
            $onboardingParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardService->verify($onboardingParameters, TelemetryPlatform::privateKey());

        }

        /**
         * @covers SecuredOnboardService::verify
         * @throws OnboardException
         * @noinspection PhpUnreachableStatementInspection
         */
        public function testGivenValidRequestTokenWhenVerifyTelemetryPlatformThenThereShouldBeAValidResponseWithAccountId()
        {
            $this->markTestSkipped('Will not run successfully without changing the registration code and Uuid.');
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $onboardService = new SecuredOnboardService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(TelemetryPlatform::applicationId());
            $onboardingParameters->setCertificationVersionId(TelemetryPlatform::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::PEM());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::http());
            $onboardingParameters->setRegistrationCode("e8c3c3c8b6");
            $onboardingParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardingResponse = $onboardService->verify($onboardingParameters, TelemetryPlatform::privateKey());

            $this->assertNotEmpty($onboardingResponse->getAccountId());
        }

        /**
         * @covers SecuredOnboardService::verify
         * @throws OnboardException
         * @noinspection PhpUnreachableStatementInspection
         */
        public function testGivenValidRequestTokenWhenVerifyOnboardingTelemetryPlatformWithWrongPrivateKeyThenThereShouldBeAnException()
        {
            $this->markTestSkipped('Will not run successfully without changing the registration code and Uuid.');
            self::expectException(OnboardException::class);
            self::expectExceptionCode(ErrorCodes::INVALID_MESSAGE);
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $onboardService = new SecuredOnboardService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(TelemetryPlatform::applicationId());
            $onboardingParameters->setCertificationVersionId(TelemetryPlatform::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::P12());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::mqtt());
            $onboardingParameters->setRegistrationCode("e8c3c3c8b6");
            $onboardingParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardService->verify($onboardingParameters, FarmingSoftware::privateKey());
        }

        /**
         * @covers AuthorizationService::authorizationUrlWithRedirect
         */
        public function testCreateOnboardLink()
        {
            $authorizationService = new AuthorizationService(new QualityAssuranceEnvironment());
            $result = $authorizationService->authorizationUrl(TelemetryPlatform::applicationId());
            $this->getLogger()->info(get_class($this) . " - authorizationUrl: ");
            $this->getLogger()->info($result->getAuthorizationUrl());
            self::assertNotEmpty($result);
        }

        /**
         * @covers AuthorizationService::parseAuthorizationToken()
         */
        public function testGetRegistrationCodeFromUri()
        {
            $uri = "state=f7ad4d4d-934e-4083-9044-e1c5f0678973&token=eyJhY2NvdW50IjoiNWQ0N2E1MzctOTQ1NS00MTBkLWFhNmQtZmJkNjlhNWNmOTkwIiwicmVnY29kZSI6ImU4YzNjM2M4YjYiLCJleHBpcmVzIjoiMjAyMS0wMi0wMVQxOTowNjoyMy45NDZaIn0%3D&signature=C%2BO2aHSRxuqDh9D%2BXL9pIDOfeZNKBWTrM0Sw8Oj3gHMEDo8crvN30JqfguMVdGvZdRikf1wr3O0JhwxIj%2FuR%2F29o98BqtundHCIjAzR7HQs5%2BdU3yLiekkpPE1JwYBb1BMlbwmf2zeccSc%2FA7aI9laGvF2W9HmTHYNuVDEJ7HBCuSIs%2FlfYBZbGEXQjOLLCJZHco2MqzzxikMk2IBJRE%2B3o6VjFMtNqASV8cO623lQOSz8i4%2BaoQu6OLjdil7V0Q%2BpXCtrsrChMJprQ6uXBAbCC3O84p8fpl6AwXxmgKioBdzZ93HlQrrRpskYGjYOgwCiYHsewoWgdvHFMrxvdO2A%3D%3D";
            $authorizationService = new AuthorizationService($this->getEnvironment());
            $authorizationResult = $authorizationService->parseAuthorizationResult($uri);
            $authorizationToken = $authorizationService->parseAuthorizationToken($authorizationResult);
            $this->getLogger()->info("RegCode: " . $authorizationToken->getRegistrationCode());
            $this->getLogger()->info("externalId: " . $authorizationResult->getState());
            $this->assertNotNull($authorizationToken->getRegistrationCode());
        }
    }
}