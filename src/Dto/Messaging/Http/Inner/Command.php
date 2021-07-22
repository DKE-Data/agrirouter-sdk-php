<?php

namespace App\Dto\Messaging\Http\Inner {

    use App\Api\Dto\JsonDeserializableInterface;
    use App\Api\Exceptions\ErrorCodes;
    use JsonException;

    /**
     * Command, found within the outbox message.
     * @package App\Dto\Messaging\Http\Inner
     */
    class Command implements JsonDeserializableInterface
    {
        private const MESSAGE = "message";

        private string $message;

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
                    case self::MESSAGE:
                        $this->message = $fieldValue;
                        break;
                    default:
                        throw new JsonException("Unknown field '$fieldName' for class '" . get_class($this) . "'.", ErrorCodes::UNKNOWN_FIELD_IN_JSON_DATA);
                        break;
                }
            }
            return $this;
        }

        public function getMessage(): string
        {
            return $this->message;
        }

        public function setMessage(string $message): void
        {
            $this->message = $message;
        }

    }
}