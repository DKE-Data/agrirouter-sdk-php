<?php declare(strict_types=1);

namespace Lib\Tests\Service\Onboard {

    use App\Api\Common\HttpClient;
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
    use Lib\Tests\Applications\TelemetryPlatform;

    /**
     * Class TelemetryPlatformSecuredOnboardServiceTest
     * @package Lib\Tests\Service\Onboard
     */
    class TelemetryPlatformSecuredOnboardServiceTest extends AbstractIntegrationTestForServices
    {
        private UtcDataService $utcDataService;
        private HttpClient $httpClient;

        public function setUp(): void
        {
            $this->utcDataService = new UtcDataService();
            $this->httpClient = $this->getHttpClient();
        }

        /**
         * @covers \App\Service\Onboard\SecuredOnboardService::onboard
         * @throws OnboardException
         */
        public function testGivenInvalidRequestTokenWhenOnboardingTelemetryPlatformThenThereShouldBeAnException()
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
        public function testGivenValidRequestTokenWhenOnboardingTelemetryPlatformThenThereShouldBeAValidResponse()
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
            $uri = "state=6eab2086-0ef2-4b64-94b0-2ce620e66ece&token=eyJhY2NvdW50IjoiNWQ0N2E1MzctOTQ1NS00MTBkLWFhNmQtZmJkNjlhNWNmOTkwIiwicmVnY29kZSI6IjI2NGQwNjgzYzkiLCJleHBpcmVzIjoiMjAyMC0wMS0xNFQxMDowOTo1OS4zMTlaIn0%3D&signature=AJOFQmO4Y%2FT8DlNOcTAfpymMFiZQBpJHr4%2FUOfrHuGpzst6UA4kQraJYJtUEKSeEaQ%2FHCf4rJlUcK14ygyGAUtGkca1Y1sUAC1lVggVnECFMnVQAyTQzSnd1DEXjqI8n4Ud4LujSF6oSbiK0DWg1U8U9swwAEQ73Z0SDna7M3OEirY8zPUhGFcRij%2FrJOEFujq2rW%2Bs267z1pnp6FNq%2BoK5nbPBuH0hvCZ57Fz3HI1VadyE77o6rOAZ1HXniGqCGr%2F6v4TqAQ22MY9xhMAfUihtwQ3VLtdHsGSu1OH%2Fs71IQczOzBgeIlMAl4mchRo3l16qSU4k4awufLq7LzDSf5Q%3D%3D";
            $authorizationService = new AuthorizationService($this->getEnvironment());
            $authorizationResult = $authorizationService->parseAuthorizationResult($uri);
            $authorizationToken = $authorizationService->parseAuthorizationToken($authorizationResult);
            $this->getLogger()->info("RegCode: " . $authorizationToken->getRegcode());
            $this->assertNotNull($authorizationToken->getRegcode());
        }
    }
}