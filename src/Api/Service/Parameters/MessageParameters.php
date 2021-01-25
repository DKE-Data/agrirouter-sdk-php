<?php

namespace App\Api\Service\Parameters {

    use App\Api\Exceptions\ValidationException;
    use App\Dto\Onboard\OnboardResponse;
    use function PHPUnit\Framework\isNull;

    /**
     * Parameter container definition.
     * @package App\Api\Service\Parameters
     */
    abstract class MessageParameters extends Parameters implements Validatable
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
            if (isNull($this->onboardResponse)) {
                throw new ValidationException("onboardResponse");
            }
        }
    }
}