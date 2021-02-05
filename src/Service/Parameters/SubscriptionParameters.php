<?php

namespace App\Service\Parameters {

    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to send subscriptions to the AR.
     * @package App\Service\Parameters
     */
    class SubscriptionParameters extends MessageParameters
    {
        private array $technicalMessageTypes;

        public function getTechnicalMessageTypes(): array
        {
            return $this->technicalMessageTypes;
        }

        public function setTechnicalMessageTypes(array $technicalMessageTypes): void
        {
            $this->technicalMessageTypes = $technicalMessageTypes;
        }

    }
}