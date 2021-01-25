<?php declare(strict_types=1);

namespace App\Service\Common {

    use App\Dto\Onboard\OnboardResponse;

    /**
     * Service to handle certificate information.
     * @package App\Service\Common
     */
    class CertificateService
    {

        /**
         * Creates the temporary certificate file.
         * @param OnboardResponse $onboardResponse The onboard response with the certificate.
         * @return string Path of the file.
         */
        public static function createCertificateFile(OnboardResponse $onboardResponse): string
        {
            $filePath = sprintf("%s/%s", sys_get_temp_dir(), $onboardResponse->getSensorAlternateId());
            if (!file_exists($filePath)) {
                $filePath = tempnam(sys_get_temp_dir(), $onboardResponse->getSensorAlternateId());
                $handle = fopen($filePath, "w");
                fwrite($handle, $onboardResponse->getAuthentication()->getCertificate());
                fclose($handle);
            }
            return $filePath;
        }

    }
}