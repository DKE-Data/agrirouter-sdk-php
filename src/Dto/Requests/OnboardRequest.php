<?php declare(strict_types=1);

namespace App\Dto\Requests {

    class OnboardRequest
    {
        private string $externalId;
        private string $applicationId;
        private string $certificationVersionId;
        private string $gatewayId;
        private string $utcTimestamp;
        private string $timezone;
        private string $certificateType;

        public function getExternalId(): string
        {
            return $this->externalId;
        }

        public function setExternalId(string $externalId): void
        {
            $this->externalId = $externalId;
        }

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

        public function getGatewayId(): string
        {
            return $this->gatewayId;
        }

        public function setGatewayId(string $gatewayId): void
        {
            $this->gatewayId = $gatewayId;
        }

        public function getUtcTimestamp(): string
        {
            return $this->utcTimestamp;
        }

        public function setUtcTimestamp(string $utcTimestamp): void
        {
            $this->utcTimestamp = $utcTimestamp;
        }


        public function getTimezone(): string
        {
            return $this->timezone;
        }

        public function setTimezone(string $timezone): void
        {
            $this->timezone = $timezone;
        }

        public function getCertificateType(): string
        {
            return $this->certificateType;
        }

        public function setCertificateType(string $certificateType): void
        {
            $this->certificateType = $certificateType;
        }

    }
}