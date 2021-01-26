<?php

namespace Lib\Tests\Service\Onboard {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OnboardException;
    use App\Definitions\ApplicationTypeDefinitions;
    use App\Definitions\CertificationTypeDefinitions;
    use App\Definitions\GatewayTypeDefinitions;
    use App\Environment\QualityAssuranceEnvironment;
    use App\Service\Common\UtcDataService;
    use App\Service\Common\UuidService;
    use App\Service\Onboard\AuthorizationService;
    use App\Service\Onboard\SecuredOnboardService;
    use App\Service\Parameters\OnboardParameters;
    use DateTime;
    use DateTimeZone;
    use GuzzleHttp\Client;
    use Lib\Tests\Applications\TelemetryPlatform;

    /**
     * Class OnboardServiceTest
     * @package Lib\Tests\Service\Onboard
     */
    class TelemetryPlatformSecuredOnboardServiceTest extends AbstractIntegrationTestForServices
    {
        private UtcDataService $utcDataService;
        private Client $httpClient;

        public function setUp(): void
        {
            $this->utcDataService = new UtcDataService();
            $this->httpClient = $this->getHttpClientService()->getHttpClient();
        }

        /**
         * @covers \App\Service\Onboard\SecuredOnboardService::onboard
         * @throws OnboardException
         */
        public function testGivenInvalidRequestTokenWhenOnboardingTPThenThereShouldBeAnException()
        {
            self::expectException(OnboardException::class);
            self::expectExceptionCode(ErrorCodes::BEARER_NOT_FOUND);

            $onboardService = new SecuredOnboardService($this->getEnvironment(), $this->utcDataService, $this->httpClient);
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(TelemetryPlatform::applicationId());
            $onboardingParameters->setCertificationVersionId(TelemetryPlatform::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::pem());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::http());
            $onboardingParameters->setRegistrationCode("INVALID");
            $onboardingParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardService->onboard($onboardingParameters, TelemetryPlatform::privateKey());

        }

        /**
         * @covers SecuredOnboardService::onboard
         * @throws OnboardException
         * @noinspection PhpUnreachableStatementInspection
         */
        public function testGivenValidRequestTokenWhenOnboardingTPThenThereShouldBeAValidResponse()
        {
            $this->markTestSkipped('Will not run successfully without changing the registration code.');

            $onboardService = new SecuredOnboardService($this->getEnvironment(), $this->utcDataService, $this->httpClient);
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(TelemetryPlatform::applicationId());
            $onboardingParameters->setCertificationVersionId(TelemetryPlatform::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::pem());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::http());
            $onboardingParameters->setRegistrationCode("98ad35b33d");
            $onboardingParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));

            $onboardingResponse = $onboardService->onboard($onboardingParameters, TelemetryPlatform::privateKey());

            $this->assertNotEmpty($onboardingResponse->getSensorAlternateId());
            $this->assertNotEmpty($onboardingResponse->getDeviceAlternateId());
            $this->assertNotEmpty($onboardingResponse->getCapabilityAlternateId());

            $this->assertNotEmpty($onboardingResponse->getAuthentication()->getCertificate());
            $this->assertNotEmpty($onboardingResponse->getAuthentication()->getSecret());
            $this->assertNotEmpty($onboardingResponse->getAuthentication()->getType());

            $this->assertNotEmpty($onboardingResponse->getConnectionCriteria()->getCommands());
            $this->assertNotEmpty($onboardingResponse->getConnectionCriteria()->getMeasures());
        }

        /**
         * @covers AuthorizationService::authorizationUrlWithRedirect
         */
        public function testCreateOnboardLink()
        {
            $this->markTestSkipped("Can be run to generate the authorization URL.");

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
            $this->markTestSkipped("Can be run to get registrationCode from URI.");
            $uri = "PASTE URI HERE";

            $authorizationService = new AuthorizationService($this->getEnvironment());
            $authorizationResult = $authorizationService->parseAuthorizationResult($uri);
            $authorizationToken = $authorizationService->parseAuthorizationToken($authorizationResult);
            $this->getLogger()->info("RegCode: " . $authorizationToken->getRegcode());
            $this->assertNotNull($authorizationToken->getRegcode());
        }
    }
}