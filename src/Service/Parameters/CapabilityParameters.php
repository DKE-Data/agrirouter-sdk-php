<?php

namespace App\Service\Parameters {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\PushNotification;
    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to send capabilities to the AR.
     * @package App\Service\Parameters
     */
    class CapabilityParameters extends MessageParameters
    {
        private ?string $applicationId = null;
        private ?string $certificationVersionId = null;
        private int $enablePushNotification = PushNotification::DISABLED;
        private array $capabilityParameters = [];

        public function getApplicationId(): string
        {
            return $this->applicationId;
        }

        public function setApplicationId(string $applicationId): void
        {
            $this->applicationId = $applicationId;
        }

        public function getCertificationVersionId(): string
        {
            return $this->certificationVersionId;
        }

        public function setCertificationVersionId(string $certificationVersionId): void
        {
            $this->certificationVersionId = $certificationVersionId;
        }

        public function getEnablePushNotification(): int
        {
            return $this->enablePushNotification;
        }

        public function setEnablePushNotification(int $enablePushNotification): void
        {
            $this->enablePushNotification = $enablePushNotification;
        }

        public function getCapabilityParameters(): array
        {
            return $this->capabilityParameters;
        }

        public function setCapabilityParameters(array $capabilityParameters): void
        {
            $this->capabilityParameters = $capabilityParameters;
        }
    }
}