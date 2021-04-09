<?php declare(strict_types=1);

namespace App\Service\Parameters {

    /**
     * Parameters for the whole onboard process.
     * @package App\Service
     */
    class OnboardParameters
    {
        private string $applicationId;
        private string $uuid;
        private string $certificationVersionId;
        private string $gatewayId;
        private string $certificationType;
        private string $registrationCode;
        private int $offset;
        private string $utcTimestamp;
        private bool $useCustomTimestamp = false;

        public function getApplicationId(): string
        {
            return $this->applicationId;
        }

        public function setApplicationId(string $applicationId): void
        {
            $this->applicationId = $applicationId;
        }

        public function getUuid(): string
        {
            return $this->uuid;
        }

        public function setUuid(string $uuid): void
        {
            $this->uuid = $uuid;
        }

        public function getCertificationVersionId(): string
        {
            return $this->certificationVersionId;
        }

        public function setCertificationVersionId(string $certificationVersionId): void
        {
            $this->certificationVersionId = $certificationVersionId;
        }

        public function getGatewayId(): string
        {
            return $this->gatewayId;
        }

        public function setGatewayId(string $gatewayId): void
        {
            $this->gatewayId = $gatewayId;
        }

        public function getCertificationType(): string
        {
            return $this->certificationType;
        }

        public function setCertificationType(string $certificationType): void
        {
            $this->certificationType = $certificationType;
        }

        public function getRegistrationCode(): string
        {
            return $this->registrationCode;
        }

        public function setRegistrationCode(string $registrationCode): void
        {
            $this->registrationCode = $registrationCode;
        }

        public function getOffset(): int
        {
            return $this->offset;
        }

        public function setOffset(int $offset): void
        {
            $this->offset = $offset;
        }

        public function getUtcTimestamp(): string
        {
            return $this->utcTimestamp;
        }

        public function setUtcTimestamp(string $utcTimestamp): void
        {
            $this->utcTimestamp = $utcTimestamp;
        }

        public function isUseCustomTimestamp(): bool
        {
            return $this->useCustomTimestamp;
        }

        public function setUseCustomTimestamp(bool $useCustomTimestamp): void
        {
            $this->useCustomTimestamp = $useCustomTimestamp;
        }

    }
}