<?php

namespace App\Api\Service\Parameters {

    use App\Api\Exceptions\ValidationException;
    use function PHPUnit\Framework\isNull;

    /**
     * Parameter container definition.
     * @package App\Api\Service\Parameters
     */
    abstract class Parameters implements Validatable
    {
        private string $applicationMessageSeqNo;
        private string $applicationMessageId;
        private string $teamSetContextId;

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

        public function getApplicationMessageSeqNo(): string
        {
            return $this->applicationMessageSeqNo;
        }

        public function setApplicationMessageSeqNo(string $applicationMessageSeqNo): void
        {
            $this->applicationMessageSeqNo = $applicationMessageSeqNo;
        }

        public function validate(): void
        {
            if (isNull($this->applicationMessageSeqNo)) {
                throw new ValidationException("applicationMessageSeqNo");
            }
            if (isNull($this->applicationMessageId)) {
                throw new ValidationException("onboardResponse");
            }
        }


    }
}