<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Dto\Onboard\OnboardingResponse;
    use App\Service\OnboardParameters;

    /**
     * Service for all onboard purposes.
     * @package App\Service\Onboard
     */
    class OnboardService
    {

        /**
         * OnboardService constructor.
         */
        public function __construct()
        {
        }

        public function onboard(OnboardParameters $onboardingParameters): ?OnboardingResponse
        {
            var_dump($onboardingParameters);
            return null;
        }

    }
}