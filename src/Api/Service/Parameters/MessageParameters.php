<?php

namespace App\Api\Service\Parameters {

    use App\Dto\Onboard\OnboardResponse;

    /**
     * Parameter container definition.
     * @package App\Api\Service\Parameters
     */
    abstract class MessageParameters extends Parameters implements ValidatableInterface
    {
        private OnboardResponse $onboardResponse;

        public function getOnboardResponse(): OnboardResponse
        {
            return $this->onboardResponse;
        }

        public function setOnboardResponse(OnboardResponse $onboardResponse): void
        {
            $this->onboardResponse = $onboardResponse;
        }

        public function validate(): void
        {
            parent::validate();
        }
    }
}