<?php declare(strict_types=1);

namespace App\Dto\Onboard {

    use App\Api\Dto\JsonDeserializableInterface;
    use App\Api\Exceptions\ErrorCodes;
    use JsonException;

    /**
     * Data transfer object for the communication.
     * @package App\Dto\Onboard
     */
    class AuthorizationToken implements JsonDeserializableInterface
    {
        private const ACCOUNT = 'account';
        private const REGISTRATION_CODE = 'regcode';
        private const EXPIRES = 'expires';

        private string $account;
        private string $registrationCode;
        private string $expires;

        public function getAccount(): ?string
        {
            return $this->account;
        }

        public function setAccount(string $account): void
        {
            $this->account = $account;
        }

        public function getRegistrationCode(): string
        {
            return $this->registrationCode;
        }

        public function setRegistrationCode(string $registrationCode): void
        {
            $this->registrationCode = $registrationCode;
        }

        public function getExpires(): string
        {
            return $this->expires;
        }

        public function setExpires(string $expires): void
        {
            $this->expires = $expires;
        }

        /**
         * @param string|mixed[] $jsonData
         * @return $this
         */
        public function jsonDeserialize($jsonData)
        {
            if (is_string($jsonData)) {
                $decodedJsonDataArray = json_decode($jsonData, true);
            } else {
                $decodedJsonDataArray = $jsonData;
            }
            foreach ($decodedJsonDataArray as $fieldName => $fieldValue) {
                switch ($fieldName) {
                    case self::ACCOUNT:
                        $this->account = $fieldValue;
                        break;
                    case self::REGISTRATION_CODE:
                        $this->registrationCode = $fieldValue;
                        break;
                    case self::EXPIRES:
                        $this->expires = $fieldValue;
                        break;
                    default:
                        throw new JsonException("Unknown field '$fieldName' for class '" . get_class($this) . "'.", ErrorCodes::UNKNOWN_FIELD_IN_JSON_DATA);
                }
            }
            return $this;
        }
    }
}