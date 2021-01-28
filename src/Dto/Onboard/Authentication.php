<?php declare(strict_types=1);

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
    class Authentication implements JsonSerializable, JsonDeserializable
    {
        private string $type;
        private string $secret;
        private string $certificate;


        #[ArrayShape(['type' => "string", 'secret' => "string", 'certificate' => "string"])]
        public function jsonSerialize(): array
        {
            return [
                'type' => $this->getType(),
                'secret' => $this->getSecret(),
                'certificate' => $this->getCertificate()
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

        public function jsonDeserialize(string|array $jsonData): self
        {
            if (is_string($jsonData)) {
                $decodedJsonDataArray = json_decode($jsonData, true);
            } else {
                $decodedJsonDataArray = $jsonData;
            }
            foreach ($decodedJsonDataArray as $fieldName => $fieldValue) {
                switch ($fieldName) {
                    case 'type':
                        $this->type = $fieldValue;
                        break;
                    case 'secret':
                        $this->secret = $fieldValue;
                        break;
                    case 'certificate':
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