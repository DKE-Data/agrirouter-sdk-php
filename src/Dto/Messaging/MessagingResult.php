<?php

namespace App\Dto\Messaging {

    /**
     * Data transfer object for the communication.
     * @package App\Dto\Messaging
     */
    class MessagingResult
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