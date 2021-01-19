<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Dto\Onboard\OnboardingResponse;
    use App\Dto\Requests\OnboardRequest;
    use App\Service\Common\UtcDataService;
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

        /**
         * Onboarding for communication units (unsecured).
         * @param OnboardParameters $onboardParameters -
         * @return OnboardingResponse|null -
         */
        public function onboard(OnboardParameters $onboardParameters): ?OnboardingResponse
        {
            $onboardRequest = new OnboardRequest();
            $onboardRequest->setExternalId($onboardParameters->getUuid());
            $onboardRequest->setApplicationId($onboardParameters->getApplicationId());
            $onboardRequest->setCertificationVersionId($onboardParameters->getCertificationVersionId());
            $onboardRequest->setGatewayId($onboardParameters->getGatewayId());
            $onboardRequest->setCertificateType($onboardParameters->getCertificationType());
            $onboardRequest->setTimezone(UtcDataService::timeZone($onboardParameters->getOffset()));
            $onboardRequest->setUtcTimestamp(UtcDataService::now());

            $requestBody = json_encode($onboardRequest);

            return null;
        }

    }
}