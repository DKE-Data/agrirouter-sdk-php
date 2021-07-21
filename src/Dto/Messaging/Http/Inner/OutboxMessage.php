<?php

namespace App\Dto\Messaging\Http\Inner {

    use App\Api\Dto\JsonDeserializableInterface;
    use App\Api\Exceptions\ErrorCodes;
    use JsonException;

    /**
     * Message within the response from the outbox.
     * @package App\Dto\Messaging\Http\Inner
     */
    class OutboxMessage implements JsonDeserializableInterface
    {
        private const CAPABILITY_ALTERNATE_ID = "capabilityAlternateId";
        private const SENSOR_ALTERNATE_ID = "sensorAlternateId";
        private const COMMAND = "command";

        private string $capabilityAlternateId;
        private string $sensorAlternateId;
        private Command $command;

        /**
         * @param mixed[]|string $jsonData
         */
        public function jsonDeserialize($jsonData): JsonDeserializableInterface
        {
            if (is_string($jsonData)) {
                $decodedJsonDataArray = json_decode($jsonData, true);
            } else {
                $decodedJsonDataArray = $jsonData;
            }
            foreach ($decodedJsonDataArray as $fieldName => $fieldValue) {
                switch ($fieldName) {
                    case self::CAPABILITY_ALTERNATE_ID:
                        $this->capabilityAlternateId = $fieldValue;
                        break;
                    case self::SENSOR_ALTERNATE_ID:
                        $this->sensorAlternateId = $fieldValue;
                        break;
                    case self::COMMAND:
                        $newCommand = new Command();
                        $newCommand->jsonDeserialize($fieldValue);
                        $this->command = $newCommand;
                        break;
                    default:
                        throw new JsonException("Unknown field '$fieldName' for class '" . get_class($this) . "'.", ErrorCodes::UNKNOWN_FIELD_IN_JSON_DATA);
                }
            }
            return $this;
        }

        public function getCapabilityAlternateId(): string
        {
            return $this->capabilityAlternateId;
        }

        public function setCapabilityAlternateId(string $capabilityAlternateId): void
        {
            $this->capabilityAlternateId = $capabilityAlternateId;
        }

        public function getSensorAlternateId(): string
        {
            return $this->sensorAlternateId;
        }

        public function setSensorAlternateId(string $sensorAlternateId): void
        {
            $this->sensorAlternateId = $sensorAlternateId;
        }

        public function getCommand(): Command
        {
            return $this->command;
        }

        public function setCommand(Command $command): void
        {
            $this->command = $command;
        }
    }
}