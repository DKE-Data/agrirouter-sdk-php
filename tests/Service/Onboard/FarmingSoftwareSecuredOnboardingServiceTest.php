<?php

namespace Lib\Tests\Service\Onboard {

    use App\Definitions\ApplicationTypeDefinitions;
    use App\Definitions\CertificationTypeDefinitions;
    use App\Definitions\GatewayTypeDefinitions;
    use App\Environment\QualityAssuranceEnvironment;
    use App\Exception\OnboardException;
    use App\Service\Common\UtcDataService;
    use App\Service\Common\UuidService;
    use App\Service\Onboard\AuthorizationService;
    use App\Service\Onboard\SecuredOnboardingService;
    use App\Service\Parameters\OnboardParameters;
    use DateTime;
    use DateTimeZone;
    use GuzzleHttp\Client;
    use Lib\Tests\Applications\FarmingSoftware;

    /**
     * Class OnboardServiceTest
     * @package Lib\Tests\Service\Onboard
     */
    class FarmingSoftwareSecuredOnboardingServiceTest extends AbstractIntegrationTestForServices
    {
        private UtcDataService $utcDataService;
        private Client $httpClient;

        public function setUp(): void
        {
            $this->utcDataService = new UtcDataService();
            $this->httpClient = $this->getHttpClientService()->getHttpClient();
        }

        /**
         * @covers \App\Service\Onboard\SecuredOnboardingService::onboard
         * @throws OnboardException
         */
        public function testGivenInvalidRequestTokenWhenOnboardingFSThenThereShouldBeAnException()
        {
            self::expectException(OnboardException::class);
            self::expectExceptionCode(401);
            self::expectExceptionMessage("Bearer not found.");

            $onboardService = new SecuredOnboardingService($this->getEnvironment(), $this->utcDataService, $this->httpClient);
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(FarmingSoftware::applicationId());
            $onboardingParameters->setCertificationVersionId(FarmingSoftware::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::p12());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::http());
            $onboardingParameters->setRegistrationCode("INVALID");
            $onboardingParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardService->onboard($onboardingParameters, FarmingSoftware::privateKey());

        }

        /**
         * @covers SecuredOnboardService::onboard
         * @throws OnboardException
         * @noinspection PhpUnreachableStatementInspection
         */
        public function testGivenValidRequestTokenWhenOnboardingFSThenThereShouldBeAValidResponse()
        {
            $this->markTestSkipped('Will not run successfully without changing the registration code.');
            $onboardService = new SecuredOnboardingService($this->getEnvironment(), $this->utcDataService, $this->httpClient);
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(FarmingSoftware::applicationId());
            $onboardingParameters->setCertificationVersionId(FarmingSoftware::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::P12());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::http());
            $onboardingParameters->setRegistrationCode("98ad35b33d");
            $onboardingParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));

            $onboardingResponse = $onboardService->onboard($onboardingParameters, FarmingSoftware::privateKey());

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
            $result = $authorizationService->authorizationUrl(FarmingSoftware::applicationId());

            $this->getLogger()->info(get_class($this)." - authorizationUrl: ");
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
            $authorizationResult = $authorizationService->parseauthorizationResult($uri);
            $authorizationToken = $authorizationService->parseAuthorizationToken($authorizationResult);
            $this->getLogger()->info("RegCode: ".$authorizationToken->getRegcode());
            $this->assertNotNull($authorizationToken->getRegcode());
        }
    }
}