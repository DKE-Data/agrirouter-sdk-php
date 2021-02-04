<?php declare(strict_types=1);

namespace Lib\Tests\Service\Onboard {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\VerificationException;
    use App\Definitions\ApplicationTypeDefinitions;
    use App\Definitions\CertificationTypeDefinitions;
    use App\Definitions\GatewayTypeDefinitions;
    use App\Environment\QualityAssuranceEnvironment;
    use App\Service\Common\UuidService;
    use App\Service\Onboard\AuthorizationService;
    use App\Service\Onboard\SecuredOnboardService;
    use App\Service\Parameters\OnboardParameters;
    use DateTime;
    use DateTimeZone;
    use Lib\Tests\Applications\FarmingSoftware;
    use Lib\Tests\Applications\TelemetryPlatform;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;

    class FarmingSoftwareSecuredOnboardVerificationServiceTest extends AbstractIntegrationTestForServices
    {
        /**
         * @covers \App\Service\Onboard\SecuredOnboardService::verify
         * @throws VerificationException
         */
        public function testGivenInvalidRequestTokenWhenVerifyOnboardFarmingSoftwareThenThereShouldBeAnException()
        {
            self::expectException(VerificationException::class);
            self::expectExceptionCode(ErrorCodes::BEARER_NOT_FOUND);

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $onboardService = new SecuredOnboardService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $onboardParameters = new OnboardParameters();
            $onboardParameters->setUuid(UuidService::newUuid());
            $onboardParameters->setApplicationId(FarmingSoftware::applicationId());
            $onboardParameters->setCertificationVersionId(FarmingSoftware::certificationVersionId());
            $onboardParameters->setApplicationType(ApplicationTypeDefinitions::APPLICATION);
            $onboardParameters->setCertificationType(CertificationTypeDefinitions::PEM);
            $onboardParameters->setGatewayId(GatewayTypeDefinitions::HTTP);
            $onboardParameters->setRegistrationCode("INVALID");
            $onboardParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardService->verify($onboardParameters, FarmingSoftware::privateKey());
        }

        /**
         * @covers SecuredOnboardService::verify
         * @throws VerificationException
         * @noinspection PhpUnreachableStatementInspection
         */
        public function testGivenValidRequestTokenWhenVerifyFarmingSoftwareThenThereShouldBeAValidResponseWithAccountId()
        {
            $this->markTestSkipped('Will not run successfully without changing the registration code and Uuid.');

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $onboardService = new SecuredOnboardService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $onboardParameters = new OnboardParameters();
            $onboardParameters->setUuid(UuidService::newUuid());
            $onboardParameters->setApplicationId(FarmingSoftware::applicationId());
            $onboardParameters->setCertificationVersionId(FarmingSoftware::certificationVersionId());
            $onboardParameters->setApplicationType(ApplicationTypeDefinitions::APPLICATION);
            $onboardParameters->setCertificationType(CertificationTypeDefinitions::PEM);
            $onboardParameters->setGatewayId(GatewayTypeDefinitions::HTTP);
            $onboardParameters->setRegistrationCode("1b6db9bda9");
            $onboardParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardResponse = $onboardService->verify($onboardParameters, FarmingSoftware::privateKey());

            $this->assertNotEmpty($onboardResponse->getAccountId());
        }

        /**
         * @covers SecuredOnboardService::verify
         * @throws VerificationException
         * @noinspection PhpUnreachableStatementInspection
         */
        public function testGivenValidRequestTokenWhenVerifyOnboardingFarmingSoftwareWithWrongPrivateKeyThenThereShouldBeAnException()
        {
            $this->markTestSkipped('Will not run successfully without changing the registration code and Uuid.');

            self::expectException(VerificationException::class);
            self::expectExceptionCode(ErrorCodes::INVALID_MESSAGE);
            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $onboardService = new SecuredOnboardService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $onboardParameters = new OnboardParameters();
            $onboardParameters->setUuid(UuidService::newUuid());
            $onboardParameters->setApplicationId(FarmingSoftware::applicationId());
            $onboardParameters->setCertificationVersionId(FarmingSoftware::certificationVersionId());
            $onboardParameters->setApplicationType(ApplicationTypeDefinitions::APPLICATION);
            $onboardParameters->setCertificationType(CertificationTypeDefinitions::PEM);
            $onboardParameters->setGatewayId(GatewayTypeDefinitions::MQTT);
            $onboardParameters->setRegistrationCode("1b6db9bda9");
            $onboardParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardService->verify($onboardParameters, TelemetryPlatform::privateKey());
        }

        /**
         * @covers AuthorizationService::authorizationUrlWithRedirect
         */
        public function testCreateOnboardLink()
        {
            $authorizationService = new AuthorizationService(new QualityAssuranceEnvironment());
            $result = $authorizationService->authorizationUrl(FarmingSoftware::applicationId());
            $this->getLogger()->info(get_class($this) . " - authorizationUrl: ");
            $this->getLogger()->info($result->getAuthorizationUrl());
            self::assertNotEmpty($result);
        }

        /**
         * @covers AuthorizationService::parseAuthorizationToken()
         */
        public function testGetRegistrationCodeFromUri()
        {
            $uri = "state=d0043ad5-4371-44fe-b980-6b306befbc19&token=eyJhY2NvdW50IjoiNWQ0N2E1MzctOTQ1NS00MTBkLWFhNmQtZmJkNjlhNWNmOTkwIiwicmVnY29kZSI6IjFiNmRiOWJkYTkiLCJleHBpcmVzIjoiMjAyMS0wMi0wMVQxODo1Mzo1MC4xNzVaIn0%3D&signature=ecqMdXHscHDr4buILeJhUOFNGNzMB8NUhKq7GyQa8TPSigITe3upeT8PFaN5ScMqInwFGCe8p1TczcI5Rfo39V%2BnY4Ruzo7lfgjD8NyOLm7h9Vxs7vJHgJSlF8Sbj5s7OMF20u%2FYCWV50OIg1lANeFoWyzl2MN4jYJQ5aTNfxJOMixKVyfoIwrCH2JTrgAStc0ZYeT87h3vdpC0oHLRckH40tN6h1%2F49%2F9lJf%2F4K9znnYWUFCJDaGCAj8yEPvxmIVZE%2BYZYpsNfPYVawnIj7lDm7gwxudU5Rl%2FVGpOTPjX2U6qwivdtkX6yMNAF5BMcmmN%2BdJoxwWw0Fktu9a%2FWBog%3D%3D";
            $authorizationService = new AuthorizationService($this->getEnvironment());
            $authorizationResult = $authorizationService->parseAuthorizationResult($uri);
            $authorizationToken = $authorizationService->parseAuthorizationToken($authorizationResult);
            $this->getLogger()->info("RegCode: " . $authorizationToken->getRegistrationCode());
            $this->getLogger()->info("externalId: " . $authorizationResult->getState());
            $this->assertNotNull($authorizationToken->getRegistrationCode());
        }
    }
}