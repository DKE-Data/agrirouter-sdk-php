<?php

namespace App\Service\Parameters {

    use Agrirouter\Cloud\Registration\OnboardingRequest\EndpointRegistrationDetails;
    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to send cloud onboard messages to the AR.
     * @package App\Service\Parameters
     */
    class CloudOnboardParameters extends MessageParameters
    {
        /**
         * @var EndpointRegistrationDetails[]
         */
        private ?array $onboardingRequests = null;

        /**
         * @return EndpointRegistrationDetails[]
         */
        public function getOnboardingRequests(): array
        {
            return $this->onboardingRequests;
        }

        /**
         * @param EndpointRegistrationDetails[] $onboardingRequests
         */
        public function setOnboardingRequests(array $onboardingRequests): void
        {
            $this->onboardingRequests = $onboardingRequests;
        }
    }
}
