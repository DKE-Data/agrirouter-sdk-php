<?php

namespace App\Api\Service\Parameters {

    /**
     * Parameter container definition.
     * @package App\Api\Service\Parameters
     */
    class MessagingParameters extends MessageParameters
    {
        private array $encodedMessages = [];

        public function getEncodedMessages(): array
        {
            return $this->encodedMessages;
        }

        public function setEncodedMessages(array $encodedMessages): void
        {
            $this->encodedMessages = $encodedMessages;
        }

    }
}