<?php declare(strict_types=1);

namespace Lib\Tests\Service\Onboard {

    use App\Api\Common\HttpClient;
    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OnboardException;
    use App\Definitions\ApplicationTypeDefinitions;
    use App\Definitions\CertificationTypeDefinitions;
    use App\Definitions\GatewayTypeDefinitions;
    use App\Service\Common\UtcDataService;
    use App\Service\Common\UuidService;
    use App\Service\Onboard\OnboardService;
    use App\Service\Parameters\OnboardParameters;
    use DateTime;
    use DateTimeZone;
    use GuzzleHttp\Client;
    use Lib\Tests\Applications\CommunicationUnit;
    use Lib\Tests\Helper\GuzzleHttpClient;

    /**
     * Class OnboardServiceTest
     * @package Lib\Tests\Service\Onboard
     */
    class OnboardServiceTest extends AbstractIntegrationTestForServices
    {
        private UtcDataService $utcDataService;
        private HttpClient $httpClient;

        public function setUp(): void
        {
            $this->utcDataService = new UtcDataService();
            $this->httpClient = new GuzzleHttpClient();
        }

        /**
         * @covers OnboardService::onboard
         */
        public function testGivenInvalidRequestTokenWhenOnboardingCommunicationUnitThenThereShouldBeAnException()
        {
            self::expectException(OnboardException::class);
            self::expectExceptionCode(ErrorCodes::BEARER_NOT_FOUND);

            $onboardService = new OnboardService($this->getEnvironment(), $this->utcDataService, $this->httpClient);
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(CommunicationUnit::applicationId());
            $onboardingParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::p12());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::http());
            $onboardingParameters->setRegistrationCode("INVALID");
            $onboardingParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardService->onboard($onboardingParameters);

        }

        /**
         * @covers OnboardService::onboard
         * @throws OnboardException
         * @noinspection PhpUnreachableStatementInspection
         */
        public function testGivenValidRequestTokenWhenOnboardingCommunicationUnitForP12ThenThereShouldBeAValidResponse()
        {
            $this->markTestIncomplete('Will not run successfully without changing the registration code.');

            $onboardService = new OnboardService($this->getEnvironment(), $this->utcDataService, $this->httpClient);
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(CommunicationUnit::applicationId());
            $onboardingParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::p12());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::http());
            $onboardingParameters->setRegistrationCode("be7cdc7c09");
            $onboardingParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardingResponse = $onboardService->onboard($onboardingParameters);

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
         * @covers OnboardService::onboard
         * @throws OnboardException
         * @noinspection PhpUnreachableStatementInspection
         */
        public function testGivenValidRequestTokenWhenOnboardingCommunicationUnitForPemThenThereShouldBeAValidResponse()
        {
            $this->markTestIncomplete('Will not run successfully without changing the registration code.');

            $onboardService = new OnboardService($this->getEnvironment(), $this->utcDataService, $this->httpClient);
            $onboardingParameters = new OnboardParameters();
            $onboardingParameters->setUuid(UuidService::newUuid());
            $onboardingParameters->setApplicationId(CommunicationUnit::applicationId());
            $onboardingParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $onboardingParameters->setApplicationType(ApplicationTypeDefinitions::application());
            $onboardingParameters->setCertificationType(CertificationTypeDefinitions::pem());
            $onboardingParameters->setGatewayId(GatewayTypeDefinitions::http());
            $onboardingParameters->setRegistrationCode("d773852334");
            $onboardingParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardingResponse = $onboardService->onboard($onboardingParameters);

            $this->assertNotEmpty($onboardingResponse->getSensorAlternateId());
            $this->assertNotEmpty($onboardingResponse->getDeviceAlternateId());
            $this->assertNotEmpty($onboardingResponse->getCapabilityAlternateId());

            $this->assertNotEmpty($onboardingResponse->getAuthentication()->getCertificate());
            $this->assertNotEmpty($onboardingResponse->getAuthentication()->getSecret());
            $this->assertNotEmpty($onboardingResponse->getAuthentication()->getType());

            $this->assertNotEmpty($onboardingResponse->getConnectionCriteria()->getCommands());
            $this->assertNotEmpty($onboardingResponse->getConnectionCriteria()->getMeasures());
        }
    }
}