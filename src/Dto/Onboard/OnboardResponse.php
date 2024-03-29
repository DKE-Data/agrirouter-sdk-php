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
    class OnboardResponse implements JsonSerializable, JsonDeserializableInterface
    {
        private const DEVICE_ALTERNATE_ID = 'deviceAlternateId';
        private const CAPABILITY_ALTERNATE_ID = 'capabilityAlternateId';
        private const SENSOR_ALTERNATE_ID = 'sensorAlternateId';
        private const CONNECTION_CRITERIA = 'connectionCriteria';
        private const AUTHENTICATION = 'authentication';

        private ?string $deviceAlternateId = null;
        private ?string $capabilityAlternateId = null;
        private ?string $sensorAlternateId = null;
        private ?ConnectionCriteria $connectionCriteria = null;
        private ?Authentication $authentication = null;

        /**
         * Serializes the object data to a simple array
         * @return array Array with object data.
         */
        #[ArrayShape([self::AUTHENTICATION => Authentication::class, self::CAPABILITY_ALTERNATE_ID => "string",
            self::CONNECTION_CRITERIA => ConnectionCriteria::class, self::DEVICE_ALTERNATE_ID => "string", self::SENSOR_ALTERNATE_ID => "string"])]
        public function jsonSerialize(): array
        {
            return [
                self::AUTHENTICATION => $this->getAuthentication(),
                self::CAPABILITY_ALTERNATE_ID => $this->getCapabilityAlternateId(),
                self::CONNECTION_CRITERIA => $this->getConnectionCriteria(),
                self::DEVICE_ALTERNATE_ID => $this->getDeviceAlternateId(),
                self::SENSOR_ALTERNATE_ID => $this->getSensorAlternateId()
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
                switch ($fieldName) {
                    case self::DEVICE_ALTERNATE_ID:
                        $this->deviceAlternateId = $fieldValue;
                        break;
                    case self::CAPABILITY_ALTERNATE_ID:
                        $this->capabilityAlternateId = $fieldValue;
                        break;
                    case self::SENSOR_ALTERNATE_ID:
                        $this->sensorAlternateId = $fieldValue;
                        break;
                    case self::CONNECTION_CRITERIA:
                        $newConnectionCriteria = new ConnectionCriteria();
                        $this->connectionCriteria = $newConnectionCriteria->jsonDeserialize($fieldValue);
                        break;
                    case self::AUTHENTICATION:
                        $newAuthentication = new Authentication();
                        $this->authentication = $newAuthentication->jsonDeserialize($fieldValue);
                        break;
                    default:
                        throw new JsonException("Unknown field '$fieldName' for class '" . get_class($this) . "'.", ErrorCodes::UNKNOWN_FIELD_IN_JSON_DATA);
                }
            }
            return $this;
        }
    }
}