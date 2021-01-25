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
    use App\Service\Onboard\OnboardService;
    use App\Service\Parameters\OnboardParameters;
    use DateTime;
    use DateTimeZone;
    use Lib\Tests\Applications\CommunicationUnit;
    use PHPUnit\Framework\TestCase;
    use GuzzleHttp\Client;

    /**
     * Class OnboardServiceTest
     * @package Lib\Tests\Service\Onboard
     */
    class OnboardServiceTest extends TestCase
    {
        private UtcDataService $utcDataService;
        private QualityAssuranceEnvironment $qualityAssuranceEnvironment;
        private Client $httpClient;

        public function setUp(): void
        {
            $this->utcDataService = new UtcDataService();
            $this->qualityAssuranceEnvironment = new QualityAssuranceEnvironment();
            $this->httpClient = new Client();
        }

        /**
         * @covers OnboardService::onboard
         */
        public function testGivenInvalidRequestTokenWhenOnboardingThenThereShouldBeAnException()
        {
            self::expectException(OnboardException::class);
            self::expectExceptionCode(ErrorCodes::BEARER_NOT_FOUND);

            $onboardService = new OnboardService($this->qualityAssuranceEnvironment, $this->utcDataService, $this->httpClient);
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
        public function testGivenValidRequestTokenWhenOnboardingForP12ThenThereShouldBeAValidResponse()
        {
            $this->markTestIncomplete('Will not run successfully without changing the registration code.');

            $onboardService = new OnboardService($this->qualityAssuranceEnvironment, $this->utcDataService, $this->httpClient);
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
        public function testGivenValidRequestTokenWhenOnboardingForPemThenThereShouldBeAValidResponse()
        {
            $this->markTestIncomplete('Will not run successfully without changing the registration code.');

            $onboardService = new OnboardService($this->qualityAssuranceEnvironment, $this->utcDataService, $this->httpClient);
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