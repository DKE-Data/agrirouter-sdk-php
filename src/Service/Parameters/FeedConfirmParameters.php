<?php

namespace App\Service\Parameters {

    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to send message confirmations to the AR.
     * @package App\Service\Parameters
     */
    class FeedConfirmParameters extends MessageParameters
    {

        /**
         * @var string[]
         */
        private array $messageIds = [];

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

    }
}