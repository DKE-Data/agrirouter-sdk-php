<?php

namespace App\Dto\Onboard {

    use App\Api\Dto\JsonDeserializable;
    use App\Api\Exceptions\ErrorCodes;
    use JetBrains\PhpStorm\ArrayShape;
    use JsonException;
    use JsonSerializable;

    /**
     * Data transfer object for the communication.
     * @package App\Dto\Onboard
     */
    class VerificationResponse implements JsonSerializable, JsonDeserializable
    {
        private const ACCOUNT_ID = 'accountId';

        private string $accountId;

        /**
         * Serializes the object data to a simple array
         * @return array Array with object data.
         */
        #[ArrayShape([self::ACCOUNT_ID => "string"])]
        public function jsonSerialize(): array
        {
            return [
                self::ACCOUNT_ID => $this->getAccountId()
            ];
        }

        public function getAccountId(): string
        {
            return $this->accountId;
        }

        public function setAccountId(string $accountId): void
        {
            $this->accountId = $accountId;
        }

        public function jsonDeserialize(array|string $jsonData): self
        {
            if (is_string($jsonData)) {
                $decodedJsonDataArray = json_decode($jsonData, true);
            } else {
                $decodedJsonDataArray = $jsonData;
            }
            foreach ($decodedJsonDataArray as $fieldName => $fieldValue) {
                switch ($fieldName) {
                    case self::ACCOUNT_ID:
                        $this->accountId = $fieldValue;
                        break;
                    default:
                        throw new JsonException("Unknown field '$fieldName' for class '" . get_class($this) . "'.", ErrorCodes::UNKNOWN_FIELD_IN_JSON_DATA);
                }
            }
            return $this;
        }
    }
}