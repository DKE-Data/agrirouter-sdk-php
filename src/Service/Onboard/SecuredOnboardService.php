<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Api\Exceptions\OnboardException;
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
            try {
                $response = $this->httpClient->sendAsync($request);
                $response->getBody()->rewind();
                $content = $response->getBody()->getContents();
                $onboardResponse = new OnboardResponse();
                $onboardResponse = $onboardResponse->jsonDeserialize($content);
                return $onboardResponse;
            } catch (Exception $exception) {
                $this->handleOnboardRequestException($exception);
            }
        }

        /**
         * Verifies an endpoint using with a prepared request. Not available in agrirouter for normal onboarding
         * @param OnboardParameters $onboardParameters The onboard parameters.
         * @param string $privateKey The private key for the secured onboard process.
         * @return VerificationResponse The verification response from the agrirouter for secured onboard requests. OnboardException for normal onboard requests.
         * @throws OnboardException Will be thrown if the onboard process was not successful.
         * @throws Exception Will be thrown in all other cases.
         */
        public function verify(OnboardParameters $onboardParameters, string $privateKey): VerificationResponse
        {
            $request = $this->createRequest($onboardParameters, $this->environment->verificationUrl(), $privateKey);
            try {
                $response = $this->httpClient->sendAsync($request);
                $response->getBody()->rewind();
                $content = $response->getBody()->getContents();
                $verificationResponse = new VerificationResponse();
                $verificationResponse = $verificationResponse->jsonDeserialize($content);
                return $verificationResponse;
            } catch (Exception $exception) {
                $this->handleOnboardRequestException($exception);
            }
        }
    }
}