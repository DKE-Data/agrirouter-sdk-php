<?php

namespace App\Dto\Requests {

    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Data transfer object for the communication.
     * @package App\Api\Messaging
     */
    class MessageRequest implements JsonSerializable
    {
        private const SENSOR_ALTERNATE_ID = 'sensorAlternateId';
        private const CAPABILITY_ALTERNATE_ID = 'capabilityAlternateId';
        private const MESSAGES = 'measures';

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

        #[ArrayShape([self::SENSOR_ALTERNATE_ID => "string", self::CAPABILITY_ALTERNATE_ID => "string", self::MESSAGES => "array"])]
        public function jsonSerialize(): array
        {
            return [
                self::SENSOR_ALTERNATE_ID => $this->getSensorAlternateId(),
                self::CAPABILITY_ALTERNATE_ID => $this->getCapabilityAlternateId(),
                self::MESSAGES => $this->getMessages()
            ];
        }
    }
}