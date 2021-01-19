<?php declare(strict_types=1);

namespace App\Service {

    /**
     * Parameters for the whole onboard process.
     * @package App\Service
     */
    class OnboardParameters
    {
        private string $applicationId;
        private string $uuid;
        private string $certificationVersionId;
        private string $GatewayId;
        private string $CertificationType;
        private string $applicationType;
        private string $registrationCode;
        private int $offset;

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
            return $this->GatewayId;
        }

        public function setGatewayId(string $GatewayId): void
        {
            $this->GatewayId = $GatewayId;
        }

        public function getCertificationType(): string
        {
            return $this->CertificationType;
        }

        public function setCertificationType(string $CertificationType): void
        {
            $this->CertificationType = $CertificationType;
        }

        public function getApplicationType(): string
        {
            return $this->applicationType;
        }

        public function setApplicationType(string $applicationType): void
        {
            $this->applicationType = $applicationType;
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

    }
}