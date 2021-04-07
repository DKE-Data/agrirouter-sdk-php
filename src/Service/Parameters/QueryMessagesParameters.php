<?php

namespace App\Service\Parameters {

    use Agrirouter\Feed\Request\ValidityPeriod;
    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to query messages.
     * @package App\Service\Parameters
     */
    class QueryMessagesParameters extends MessageParameters
    {
        /**
         * @var string[]
         */
        private array $messageIds = [];
        /**
         * @var string[]
         */
        private array $senders = [];
        private ?ValidityPeriod $validityPeriod = null;

        /**
         * @return string[]
         */
        public function getMessageIds(): array
        {
            return $this->messageIds;
        }

        /**
         * @param string[] $messageIds
         */
        public function setMessageIds(array $messageIds): void
        {
            $this->messageIds = $messageIds;
        }

        /**
         * @return string[]
         */
        public function getSenders(): array
        {
            return $this->senders;
        }

        /**
         * @param string[] $senders
         */
        public function setSenders(array $senders): void
        {
            $this->senders = $senders;
        }

        public function getValidityPeriod(): ?ValidityPeriod
        {
            return $this->validityPeriod;
        }

        public function setValidityPeriod(?ValidityPeriod $validityPeriod): void
        {
            $this->validityPeriod = $validityPeriod;
        }

    }
}