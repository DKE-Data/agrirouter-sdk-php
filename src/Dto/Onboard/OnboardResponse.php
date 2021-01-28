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
    class OnboardResponse implements JsonSerializable, JsonDeserializable
    {
        private string $deviceAlternateId;

        private string $capabilityAlternateId;

        private string $sensorAlternateId;

        private ConnectionCriteria $connectionCriteria;

        private Authentication $authentication;

        /**
         * Serializes the object data to a simple array
         * @return array Array with object data.
         */
        #[ArrayShape(['authentication' => Authentication::class, 'capabilityAlternateId' => "string",
            'connectionCriteria' => ConnectionCriteria::class, 'deviceAlternateId' => "string", 'sensorAlternateId' => "string"])]
        public function jsonSerialize(): array
        {
            return [
                'authentication' => $this->getAuthentication(),
                'capabilityAlternateId' => $this->getCapabilityAlternateId(),
                'connectionCriteria' => $this->getConnectionCriteria(),
                'deviceAlternateId' => $this->getDeviceAlternateId(),
                'sensorAlternateId' => $this->getSensorAlternateId()
            ];
        }

        public function getAuthentication(): Authentication
        {
            return $this->authentication;
        }

        public function setAuthentication(Authentication $authentication): void
        {
            $this->authentication = $authentication;
        }

        public function getCapabilityAlternateId(): string
        {
            return $this->capabilityAlternateId;
        }

        public function setCapabilityAlternateId(string $capabilityAlternateId): void
        {
            $this->capabilityAlternateId = $capabilityAlternateId;
        }

        public function getConnectionCriteria(): ConnectionCriteria
        {
            return $this->connectionCriteria;
        }

        public function setConnectionCriteria(ConnectionCriteria $connectionCriteria): void
        {
            $this->connectionCriteria = $connectionCriteria;
        }

        public function getDeviceAlternateId(): string
        {
            return $this->deviceAlternateId;
        }

        public function setDeviceAlternateId(string $deviceAlternateId): void
        {
            $this->deviceAlternateId = $deviceAlternateId;
        }

        public function getSensorAlternateId(): string
        {
            return $this->sensorAlternateId;
        }

        public function setSensorAlternateId(string $sensorAlternateId): void
        {
            $this->sensorAlternateId = $sensorAlternateId;
        }

        public function jsonDeserialize(array|string $jsonData): self
        {
            if (is_string($jsonData)) {
                $decodedJsonDataArray = json_decode($jsonData, true);
            } else {
                $decodedJsonDataArray = $jsonData;
            }
            foreach ($decodedJsonDataArray as $fieldName => $fieldValue) {
                switch ($fieldName){
                    case 'deviceAlternateId':
                        $this->deviceAlternateId = $fieldValue;
                        break;
                    case 'capabilityAlternateId':
                        $this->capabilityAlternateId = $fieldValue;
                        break;
                    case 'sensorAlternateId':
                        $this->sensorAlternateId = $fieldValue;
                        break;
                    case 'connectionCriteria':
                        $newConnectionCriteria = new ConnectionCriteria();
                        $this->connectionCriteria = $newConnectionCriteria->jsonDeserialize($fieldValue);
                        break;
                    case'authentication':
                        $newAuthentication = new Authentication();
                        $this->authentication = $newAuthentication->jsonDeserialize($fieldValue);
                        break;
                    default:
                        throw new JsonException("Unknown field '$fieldName' for class '".get_class($this)."'.", ErrorCodes::UNKNOWN_FIELD_IN_JSON_DATA);
                }
            }
            return $this;
        }
    }
}