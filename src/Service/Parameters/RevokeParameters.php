<?php

namespace App\Service\Parameters {

    /**
     * Parameters for the revoke process.
     * @package App\Service\Parameters
     */
    class RevokeParameters
    {
        private string $accountId;
        private array $endpointIds;
        private string $applicationId;
        private int $offset;
        private string $utcTimestamp;
        private bool $useCustomTimestamp = false;

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

        public function getApplicationId(): string
        {
            return $this->applicationId;
        }

        public function setApplicationId(string $applicationId): void
        {
            $this->applicationId = $applicationId;
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