<?php

namespace App\Service\Parameters {

    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to send message confirmations to the AR.
     * @package App\Service\Parameters
     */
    class FeedConfirmParameters extends MessageParameters
    {

        private array $messageIds = [];

        public function getMessageIds(): array
        {
            return $this->messageIds;
        }

        public function setMessageIds(array $messageIds): void
        {
            $this->messageIds = $messageIds;
        }

    }
}