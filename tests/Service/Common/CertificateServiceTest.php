<?php

namespace Lib\Tests\Service\Common {

    use App\Dto\Onboard\Inner\Authentication;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Common\CertificateService;
    use App\Service\Common\UuidService;
    use PHPUnit\Framework\TestCase;
    use function PHPUnit\Framework\assertTrue;

    class CertificateServiceTest extends TestCase
    {

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

    }
}