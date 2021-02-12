<?php declare(strict_types=1);

namespace Lib\Tests\Service\Onboard {

    use App\Api\Exceptions\DecodeMessageException;
    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OnboardException;
    use App\Definitions\CertificationTypeDefinitions;
    use App\Definitions\GatewayTypeDefinitions;
    use App\Environment\QualityAssuranceEnvironment;
    use App\Service\Common\UuidService;
    use App\Service\Onboard\AuthorizationService;
    use App\Service\Onboard\SecuredOnboardService;
    use App\Service\Parameters\OnboardParameters;
    use DateTime;
    use DateTimeZone;
    use Lib\Tests\Applications\TelemetryPlatform;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;

    class TelemetryPlatformSecuredOnboardServiceTest extends AbstractIntegrationTestForServices
    {

        /**
         * @covers \App\Service\Onboard\SecuredOnboardService::onboard
         * @throws OnboardException
         */
        public function testGivenInvalidRequestTokenWhenOnboardTelemetryPlatformThenThereShouldBeAnException()
        {
            self::expectException(OnboardException::class);
            self::expectExceptionCode(ErrorCodes::BEARER_NOT_FOUND);

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $onboardService = new SecuredOnboardService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $onboardParameters = new OnboardParameters();
            $onboardParameters->setUuid(UuidService::newUuid());
            $onboardParameters->setApplicationId(TelemetryPlatform::applicationId());
            $onboardParameters->setCertificationVersionId(TelemetryPlatform::certificationVersionId());
            $onboardParameters->setCertificationType(CertificationTypeDefinitions::PEM);
            $onboardParameters->setGatewayId(GatewayTypeDefinitions::HTTP);
            $onboardParameters->setRegistrationCode("INVALID");
            $onboardParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardService->onboard($onboardParameters, TelemetryPlatform::privateKey());

        }

        /**
         * @covers SecuredOnboardService::onboard
         * @throws OnboardException
         * @noinspection PhpUnreachableStatementInspection
         */
        public function testGivenValidRequestTokenWhenOnboardTelemetryPlatformThenThereShouldBeAValidResponse()
        {
            $this->markTestSkipped('Will not run successfully without changing the registration code.');

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $onboardService = new SecuredOnboardService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $onboardParameters = new OnboardParameters();
            $onboardParameters->setUuid(UuidService::newUuid());
            $onboardParameters->setApplicationId(TelemetryPlatform::applicationId());
            $onboardParameters->setCertificationVersionId(TelemetryPlatform::certificationVersionId());
            $onboardParameters->setCertificationType(CertificationTypeDefinitions::PEM);
            $onboardParameters->setGatewayId(GatewayTypeDefinitions::HTTP);
            $onboardParameters->setRegistrationCode("98ad35b33d");
            $onboardParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardResponse = $onboardService->onboard($onboardParameters, TelemetryPlatform::privateKey());

            $this->assertNotEmpty($onboardResponse->getSensorAlternateId());
            $this->assertNotEmpty($onboardResponse->getDeviceAlternateId());
            $this->assertNotEmpty($onboardResponse->getCapabilityAlternateId());

            $this->assertNotEmpty($onboardResponse->getAuthentication()->getCertificate());
            $this->assertNotEmpty($onboardResponse->getAuthentication()->getSecret());
            $this->assertNotEmpty($onboardResponse->getAuthentication()->getType());

            $this->assertNotEmpty($onboardResponse->getConnectionCriteria()->getCommands());
            $this->assertNotEmpty($onboardResponse->getConnectionCriteria()->getMeasures());
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
         * @throws DecodeMessageException
         */
        public function testGetRegistrationCodeFromUri()
        {
            $uri = "state=6eab2086-0ef2-4b64-94b0-2ce620e66ece&token=eyJhY2NvdW50IjoiNWQ0N2E1MzctOTQ1NS00MTBkLWFhNmQtZmJkNjlhNWNmOTkwIiwicmVnY29kZSI6IjI2NGQwNjgzYzkiLCJleHBpcmVzIjoiMjAyMC0wMS0xNFQxMDowOTo1OS4zMTlaIn0%3D&signature=AJOFQmO4Y%2FT8DlNOcTAfpymMFiZQBpJHr4%2FUOfrHuGpzst6UA4kQraJYJtUEKSeEaQ%2FHCf4rJlUcK14ygyGAUtGkca1Y1sUAC1lVggVnECFMnVQAyTQzSnd1DEXjqI8n4Ud4LujSF6oSbiK0DWg1U8U9swwAEQ73Z0SDna7M3OEirY8zPUhGFcRij%2FrJOEFujq2rW%2Bs267z1pnp6FNq%2BoK5nbPBuH0hvCZ57Fz3HI1VadyE77o6rOAZ1HXniGqCGr%2F6v4TqAQ22MY9xhMAfUihtwQ3VLtdHsGSu1OH%2Fs71IQczOzBgeIlMAl4mchRo3l16qSU4k4awufLq7LzDSf5Q%3D%3D";
            $authorizationService = new AuthorizationService($this->getEnvironment());
            $authorizationResult = $authorizationService->parseAuthorizationResult($uri);
            $authorizationToken = $authorizationService->parseAuthorizationToken($authorizationResult);
            $this->getLogger()->info("RegCode: " . $authorizationToken->getRegistrationCode());
            $this->assertNotNull($authorizationToken->getRegistrationCode());
        }
    }
}