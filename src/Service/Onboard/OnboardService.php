<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Parameters\OnboardParameters;
    use Exception;

    /**
     * Service for all unsecured onboard purposes.
     * @package App\Service\Onboard
     */
    class OnboardService extends AbstractOnboardService
    {
        public function onboard(OnboardParameters $onboardParameters, ?string $privateKey = null): OnboardResponse
        {
            $request = $this->createRequest($onboardParameters, $this->environment->onboardUrl(), $privateKey);
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
    }
}