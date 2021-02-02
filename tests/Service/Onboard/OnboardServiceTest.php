<?php declare(strict_types=1);

namespace Lib\Tests\Service\Onboard {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OnboardException;
    use App\Definitions\ApplicationTypeDefinitions;
    use App\Definitions\CertificationTypeDefinitions;
    use App\Definitions\GatewayTypeDefinitions;
    use App\Service\Common\UuidService;
    use App\Service\Onboard\OnboardService;
    use App\Service\Parameters\OnboardParameters;
    use DateTime;
    use DateTimeZone;
    use Lib\Tests\Applications\CommunicationUnit;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;

    class OnboardServiceTest extends AbstractIntegrationTestForServices
    {
        /**
         * @covers OnboardService::onboard
         */
        public function testGivenInvalidRequestTokenWhenOnboardCommunicationUnitThenThereShouldBeAnException()
        {
            self::expectException(OnboardException::class);
            self::expectExceptionCode(ErrorCodes::BEARER_NOT_FOUND);

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $onboardService = new OnboardService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $onboardParameters = new OnboardParameters();
            $onboardParameters->setUuid(UuidService::newUuid());
            $onboardParameters->setApplicationId(CommunicationUnit::applicationId());
            $onboardParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $onboardParameters->setApplicationType(ApplicationTypeDefinitions::APPLICATION);
            $onboardParameters->setCertificationType(CertificationTypeDefinitions::PEM);
            $onboardParameters->setGatewayId(GatewayTypeDefinitions::HTTP);
            $onboardParameters->setRegistrationCode("INVALID");
            $onboardParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardService->onboard($onboardParameters);

        }

        /**
         * @covers OnboardService::onboard
         * @throws OnboardException
         * @noinspection PhpUnreachableStatementInspection
         */
        public function testGivenValidRequestTokenWhenOnboardCommunicationUnitForPemThenThereShouldBeAValidResponse()
        {
            $this->markTestIncomplete('Will not run successfully without changing the registration code.');

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $onboardService = new OnboardService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $onboardParameters = new OnboardParameters();
            $onboardParameters->setUuid(UuidService::newUuid());
            $onboardParameters->setApplicationId(CommunicationUnit::applicationId());
            $onboardParameters->setCertificationVersionId(CommunicationUnit::certificationVersionId());
            $onboardParameters->setApplicationType(ApplicationTypeDefinitions::APPLICATION);
            $onboardParameters->setCertificationType(CertificationTypeDefinitions::PEM);
            $onboardParameters->setGatewayId(GatewayTypeDefinitions::HTTP);
            $onboardParameters->setRegistrationCode("d773852334");
            $onboardParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $onboardResponse = $onboardService->onboard($onboardParameters);

            $this->assertNotEmpty($onboardResponse->getSensorAlternateId());
            $this->assertNotEmpty($onboardResponse->getDeviceAlternateId());
            $this->assertNotEmpty($onboardResponse->getCapabilityAlternateId());

            $this->assertNotEmpty($onboardResponse->getAuthentication()->getCertificate());
            $this->assertNotEmpty($onboardResponse->getAuthentication()->getSecret());
            $this->assertNotEmpty($onboardResponse->getAuthentication()->getType());

            $this->assertNotEmpty($onboardResponse->getConnectionCriteria()->getCommands());
            $this->assertNotEmpty($onboardResponse->getConnectionCriteria()->getMeasures());
        }
    }
}