<?php

namespace App\Dto\Messaging {

    /**
     * Data transfer object for the communication.
     * @package App\Api\Messaging
     */
    class MessageRequest
    {
        private string $sensorAlternateId;
        private string $capabilityAlternateId;
        private array $messages;

        public function getSensorAlternateId(): string
        {
            return $this->sensorAlternateId;
        }

        public function setSensorAlternateId(string $sensorAlternateId): void
        {
            $this->sensorAlternateId = $sensorAlternateId;
        }

        public function getCapabilityAlternateId(): string
        {
            return $this->capabilityAlternateId;
        }

        public function setCapabilityAlternateId(string $capabilityAlternateId): void
        {
            $this->capabilityAlternateId = $capabilityAlternateId;
        }

        public function getMessages(): array
        {
            return $this->messages;
        }

        public function setMessages(array $messages): void
        {
            $this->messages = $messages;
        }


    }
}