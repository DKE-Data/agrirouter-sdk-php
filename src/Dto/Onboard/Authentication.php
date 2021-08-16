<?php declare(strict_types=1);

namespace App\Dto\Onboard {

    use App\Api\Dto\JsonDeserializableInterface;
    use App\Api\Exceptions\ErrorCodes;
    use JetBrains\PhpStorm\ArrayShape;
    use JsonException;
    use JsonSerializable;

    /**
     * Data transfer object for the communication.
     * @package App\Dto\Onboard
     */
    class Authentication implements JsonSerializable, JsonDeserializableInterface
    {
        private const TYPE = 'type';
        private const SECRET = 'secret';
        private const CERTIFICATE = 'certificate';

        private ?string $type = null;
        private ?string $secret = null;
        private ?string $certificate = null;


        public function jsonSerialize(): array
        {
            return [
                self::TYPE => $this->getType(),
                self::SECRET => $this->getSecret(),
                self::CERTIFICATE => $this->getCertificate()
            ];
        }

        public function getType(): string
        {
            return $this->type;
        }

        public function setType(string $type): void
        {
            $this->type = $type;
        }

        public function getSecret(): string
        {
            return $this->secret;
        }

        public function setSecret(string $secret): void
        {
            $this->secret = $secret;
        }

        public function getCertificate(): string
        {
            return $this->certificate;
        }

        public function setCertificate(string $certificate): void
        {
            $this->certificate = $certificate;
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
                    case self::TYPE:
                        $this->type = $fieldValue;
                        break;
                    case self::SECRET:
                        $this->secret = $fieldValue;
                        break;
                    case self::CERTIFICATE:
                        $this->certificate = $fieldValue;
                        break;
                    default:
                        throw new JsonException("Unknown field '$fieldName' for class '" . get_class($this) . "'.", ErrorCodes::UNKNOWN_FIELD_IN_JSON_DATA);
                }
            }
            return $this;
        }
    }
}