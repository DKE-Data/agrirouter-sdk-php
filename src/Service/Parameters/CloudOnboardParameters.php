<?php

namespace App\Service\Parameters {

    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to send cloud onboard messages to the AR.
     * @package App\Service\Parameters
     */
    class CloudOnboardParameters extends MessageParameters
    {
        private ?array $onboardingRequests = null;

        public function getOnboardingRequests(): array
        {
            return $this->onboardingRequests;
        }

        public function setOnboardingRequests(array $onboardingRequests): void
        {
            $this->onboardingRequests = $onboardingRequests;
        }
    }
}