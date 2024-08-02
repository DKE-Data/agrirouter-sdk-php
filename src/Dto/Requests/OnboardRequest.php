<?php declare(strict_types=1);

namespace App\Dto\Requests {

    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Data transfer object for the communication.
     * @package App\Dto\Requests
     */
    class OnboardRequest implements JsonSerializable
    {
        private const ID = 'id';
        private const APPLICATION_ID = 'applicationId';
        private const CERTIFICATION_VERSION_ID = 'certificationVersionId';
        private const GATEWAY_ID = 'gatewayId';
        private const UTC_TIMESTAMP = 'UTCTimestamp';
        private const TIME_ZONE = 'timeZone';
        private const CERTIFICATE_TYPE = 'certificateType';

        private ?string $externalId = null;
        private ?string $applicationId = null;
        private ?string $certificationVersionId = null;
        private ?string $gatewayId = null;
        private ?string $utcTimestamp = null;
        private ?string $timeZone = null;
        private ?string $certificateType = null;

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

        #[ArrayShape([self::ID => "string", self::APPLICATION_ID => "string", self::CERTIFICATION_VERSION_ID => "string", self::GATEWAY_ID => "string", self::UTC_TIMESTAMP => "string", self::TIME_ZONE => "string", self::CERTIFICATE_TYPE => "string"])]
        public function jsonSerialize(): array
        {
            return [
                self::ID => $this->getExternalId(),
                self::APPLICATION_ID => $this->getApplicationId(),
                self::CERTIFICATION_VERSION_ID => $this->getCertificationVersionId(),
                self::GATEWAY_ID => $this->getGatewayId(),
                self::UTC_TIMESTAMP => $this->getUtcTimestamp(),
                self::TIME_ZONE => $this->getTimeZone(),
                self::CERTIFICATE_TYPE => $this->getCertificateType()
            ];
        }
    }
}
