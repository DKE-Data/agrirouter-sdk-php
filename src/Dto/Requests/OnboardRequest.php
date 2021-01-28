<?php declare(strict_types=1);

namespace App\Dto\Requests {

    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Class OnboardRequest - Data transfer object for the communication.
     * @package App\Dto\Requests
     */
    class OnboardRequest implements JsonSerializable
    {
        private string $externalId;
        private string $applicationId;
        private string $certificationVersionId;
        private string $gatewayId;
        private string $utcTimestamp;
        private string $timeZone;
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

        public function getTimeZone(): string
        {
            return $this->timeZone;
        }

        public function setTimeZone(string $timeZone): void
        {
            $this->timeZone = $timeZone;
        }

        public function getCertificateType(): string
        {
            return $this->certificateType;
        }

        public function setCertificateType(string $certificateType): void
        {
            $this->certificateType = $certificateType;
        }

        #[ArrayShape(['id' => "string", 'applicationId' => "string", 'certificationVersionId' => "string", 'gatewayId' => "string", 'UTCTimestamp' => "string", 'timeZone' => "string", 'certificateType' => "string"])]
        public function jsonSerialize(): array
        {
            return [
                'id' => $this->getExternalId(),
                'applicationId' => $this->getApplicationId(),
                'certificationVersionId' => $this->getCertificationVersionId(),
                'gatewayId' => $this->getGatewayId(),
                'UTCTimestamp' => $this->getUtcTimestamp(),
                'timezone' => $this->getTimeZone(),
                'certificateType' => $this->getCertificateType()
            ];
        }
    }
}