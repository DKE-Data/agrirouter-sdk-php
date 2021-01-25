<?php

namespace Lib\Tests\Service\Common {

    use App\Dto\Onboard\Authentication;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Common\CertificateService;
    use App\Service\Common\UuidService;
    use Error;
    use PHPUnit\Framework\TestCase;
    use TypeError;
    use function PHPUnit\Framework\assertTrue;

    class CertificateServiceTest extends TestCase
    {

        /**
         * @covers CertificateService::createCertificateFile()
         */
        function testGivenValidOnboardResponseWhenCreatingTheCertificateFileThenTheServiceShouldReturnAValidPath()
        {
            $onboardResponse = new OnboardResponse();
            $onboardResponse->setSensorAlternateId(UuidService::newUuid());
            $authentication = new Authentication();
            $authentication->setCertificate("MY CERTIFICATE!");
            $onboardResponse->setAuthentication($authentication);
            $certificateFile = CertificateService::createCertificateFile($onboardResponse);
            assertTrue(file_exists($certificateFile));
        }

        /**
         * @covers CertificateService::createCertificateFile()
         */
        function testGivenValidOnboardResponseWhenCreatingTheCertificateFileAgainThenTheServiceShouldReturnAValidPath()
        {
            $onboardResponse = new OnboardResponse();
            $onboardResponse->setSensorAlternateId(UuidService::newUuid());
            $authentication = new Authentication();
            $authentication->setCertificate("MY CERTIFICATE!");
            $onboardResponse->setAuthentication($authentication);
            $certificateFile = CertificateService::createCertificateFile($onboardResponse);
            assertTrue(file_exists($certificateFile));

            $certificateFile = CertificateService::createCertificateFile($onboardResponse);
            assertTrue(file_exists($certificateFile));
        }

        /**
         * @covers CertificateService::createCertificateFile()
         */
        function testGivenInvalidOnboardResponseWhenCreatingTheCertificateFileThenTheServiceShouldThrowAnException()
        {
            self::expectException(Error::class);

            $onboardResponse = new OnboardResponse();
            $onboardResponse->setSensorAlternateId(UuidService::newUuid());
            $authentication = new Authentication();
            $onboardResponse->setAuthentication($authentication);
            $certificateFile = CertificateService::createCertificateFile($onboardResponse);
            assertTrue(file_exists($certificateFile));
        }

        /**
         * @covers CertificateService::createCertificateFile()
         */
        function testGivenNullValueForOnboardResponseWhenCreatingTheCertificateFileThenTheServiceShouldThrowAnException()
        {
            self::expectException(TypeError::class);

            $certificateFile = CertificateService::createCertificateFile(null);
            assertTrue(file_exists($certificateFile));
        }

    }
}