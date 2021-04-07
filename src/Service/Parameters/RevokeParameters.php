<?php

namespace App\Service\Parameters {

    /**
     * Parameters for the revoke process.
     * @package App\Service\Parameters
     */
    class RevokeParameters
    {
        private string $accountId;
        /**
         * @var string[]
         */
        private array $endpointIds;
        private string $applicationId;
        private int $offset;

        public function getAccountId(): string
        {
            return $this->accountId;
        }

        public function setAccountId(string $accountId): void
        {
            $this->accountId = $accountId;
        }

        /**
         * @return string[]
         */
        public function getEndpointIds(): array
        {
            return $this->endpointIds;
        }

        /**
         * @param string[] $endpointIds
         */
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
    }
}