<?php declare(strict_types=1);

namespace App\Dto\Requests {

    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Data transfer object for the communication.
     * @package App\Dto\Requests
     */
    class RevokeRequest implements JsonSerializable
    {
        private const ACCOUNT_ID = 'accountId';
        private const ENDPOINT_IDS = 'endpointIds';
        private const UTC_TIMESTAMP = 'UTCTimestamp';
        private const TIME_ZONE = 'timezone';

        private ?string $accountId = null;
        private ?array $endpointIds = null;
        private ?string $utcTimestamp = null;
        private ?string $timeZone = null;

        #[ArrayShape([self::ACCOUNT_ID => "string", self::ENDPOINT_IDS => "array", self::UTC_TIMESTAMP => "string", self::TIME_ZONE => "string"])]
        public function jsonSerialize(): array
        {
            return [
                self::ACCOUNT_ID => $this->getAccountId(),
                self::ENDPOINT_IDS => $this->getEndpointIds(),
                self::UTC_TIMESTAMP => $this->getUtcTimestamp(),
                self::TIME_ZONE => $this->getTimeZone()
            ];
        }

        public function getAccountId(): string
        {
            return $this->accountId;
        }

        public function setAccountId(string $accountId): void
        {
            $this->accountId = $accountId;
        }

        public function getEndpointIds(): array
        {
            return $this->endpointIds;
        }

        public function setEndpointIds(array $endpointIds): void
        {
            $this->endpointIds = $endpointIds;
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
    }
}