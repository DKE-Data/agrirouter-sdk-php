<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\VerificationException;
    use App\Dto\Onboard\OnboardResponse;
    use App\Dto\Onboard\VerificationResponse;
    use App\Service\Parameters\OnboardParameters;
    use Exception;

    /**
     * Service for all secured onboard purposes.
     * @package App\Service\Onboard
     */
    class SecuredOnboardService extends AbstractOnboardService
    {
        public function onboard(OnboardParameters $onboardParameters, ?string $privateKey = null): OnboardResponse
        {
            $request = $this->createRequest($onboardParameters, $this->environment->securedOnboardUrl(), $privateKey);
            return $this->send($request);
        }

        /**
         * Verifies an endpoint using with a prepared request. Not available in agrirouter for the common onboard process.
         * @param OnboardParameters $onboardParameters The onboard parameters.
         * @param string $privateKey The private key for the secured onboard process.
         * @return VerificationResponse The verification response from the agrirouter for secured onboard requests. OnboardException for normal onboard requests.
         * @throws VerificationException Will be thrown if the onboard process was not successful.
         * @throws Exception
         */
        public function verify(OnboardParameters $onboardParameters, string $privateKey): VerificationResponse
        {
            $request = $this->createRequest($onboardParameters, $this->environment->verificationUrl(), $privateKey);
            try {
                $response = $this->httpClient->sendRequest($request);
                $response->getBody()->rewind();
                $content = $response->getBody()->getContents();
                $verificationResponse = new VerificationResponse();
                $verificationResponse = $verificationResponse->jsonDeserialize($content);
                return $verificationResponse;
            } catch (Exception $exception) {
                if ($exception->getCode() == 400) {
                    throw new VerificationException($exception->getMessage(), ErrorCodes::INVALID_MESSAGE);
                } elseif ($exception->getCode() == 401) {
                    throw new VerificationException($exception->getMessage(), ErrorCodes::BEARER_NOT_FOUND);
                } else {
                    throw new VerificationException($exception->getMessage(), ErrorCodes::UNDEFINED);
                }
            }
        }
    }
}