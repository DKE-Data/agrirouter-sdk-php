<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Parameters\OnboardParameters;

    /**
     * Service for all unsecured onboard purposes.
     * @package App\Service\Onboard
     */
    class OnboardService extends AbstractOnboardService
    {
        public function onboard(OnboardParameters $onboardParameters, ?string $privateKey = null): OnboardResponse
        {
            $request = $this->createRequest($onboardParameters, $this->environment->onboardUrl(), $privateKey);
            return $this->sendRequest($request);
        }
    }
}