<?php

namespace App\Api\Service\Parameters {

    /**
     * Parameter container definition.
     * @package App\Api\Service\Parameters
     */
    abstract class Parameters implements ValidatableInterface
    {
        private int $applicationMessageSeqNo;
        private ?string $applicationMessageId = "";
        private ?string $teamSetContextId = "";

        public function getApplicationMessageId(): string
        {
            return $this->applicationMessageId;
        }

        public function setApplicationMessageId(string $applicationMessageId): void
        {
            $this->applicationMessageId = $applicationMessageId;
        }

        public function getTeamSetContextId(): string
        {
            return $this->teamSetContextId;
        }

        public function setTeamSetContextId(string $teamSetContextId): void
        {
            $this->teamSetContextId = $teamSetContextId;
        }

        public function getApplicationMessageSeqNo(): int
        {
            return $this->applicationMessageSeqNo;
        }

        public function setApplicationMessageSeqNo(int $applicationMessageSeqNo): void
        {
            $this->applicationMessageSeqNo = $applicationMessageSeqNo;
        }

        public function validate(): void
        {
            // Empty by default.
        }

    }
}