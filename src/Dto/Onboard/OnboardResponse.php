<?php declare(strict_types=1);

namespace App\Dto\Onboard {

    use App\Api\Dto\JsonDeserializableInterface;
    use Exception;
    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Data transfer object for the communication.
     * @package App\Dto\Onboard
     */
    class OnboardResponse implements JsonSerializable, JsonDeserializableInterface
    {
        private string $deviceAlternateId;

        private string $capabilityAlternateId;

        private string $sensorAlternateId;

        private ConnectionCriteria $connectionCriteria;

        private Authentication $authentication;

        public function getDeviceAlternateId(): string
        {
            return $this->deviceAlternateId;
        }

        public function setDeviceAlternateId(string $deviceAlternateId): void
        {
            $this->deviceAlternateId = $deviceAlternateId;
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

        public function getConnectionCriteria(): ConnectionCriteria
        {
            return $this->connectionCriteria;
        }

        public function setConnectionCriteria(ConnectionCriteria $connectionCriteria): void
        {
            $this->connectionCriteria = $connectionCriteria;
        }

        public function getAuthentication(): Authentication
        {
            return $this->authentication;
        }

        public function setAuthentication(Authentication $authentication): void
        {
            $this->authentication = $authentication;
        }

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

        public function jsonDeserialize(array $data): self
        {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $classname = __NAMESPACE__ . '\\' . ucfirst($key);
                    $object = new $classname();
                    $this->$key = $object->jsonDeserialize($value);
                } else {
                    try {
                        $this->$key = $value;
                    } catch (Exception $ex) {
                        echo $ex;
                    }
                }
            }
            return $this;
        }
    }
}