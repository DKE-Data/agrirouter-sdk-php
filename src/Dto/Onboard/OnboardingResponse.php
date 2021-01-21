<?php declare(strict_types=1);

namespace App\Dto\Onboard {

    use App\Helper\JsonDeserializable;
    use Exception;
    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Class OnboardingResponse - Data transfer object for the communication.
     * @package App\Dto\Onboard
     */
    class OnboardingResponse implements JsonSerializable,JsonDeserializable
    {
        /**
         * @var string Device alternate ID.
         */
        public string $deviceAlternateId;
        /**
         * @var string Capability alternate ID.
         */
        public string $capabilityAlternateId;
        /**
         * @var string Sensor alternate ID.
         */
        public string $sensorAlternateId;
        /**
         * @var ConnectionCriteria Connection criteria.
         */
        public ConnectionCriteria $connectionCriteria;
        /**
         * @var Authentication Authentication.
         */
        public Authentication $authentication;

        /**
         * @return string
         */
        public function getDeviceAlternateId(): string
        {
            return $this->deviceAlternateId;
        }

        /**
         * @param string $deviceAlternateId
         */
        public function setDeviceAlternateId(string $deviceAlternateId): void
        {
            $this->deviceAlternateId = $deviceAlternateId;
        }

        /**
         * @return string
         */
        public function getCapabilityAlternateId(): string
        {
            return $this->capabilityAlternateId;
        }

        /**
         * @param string $capabilityAlternateId
         */
        public function setCapabilityAlternateId(string $capabilityAlternateId): void
        {
            $this->capabilityAlternateId = $capabilityAlternateId;
        }

        /**
         * @return string
         */
        public function getSensorAlternateId(): string
        {
            return $this->sensorAlternateId;
        }

        /**
         * @param string $sensorAlternateId
         */
        public function setSensorAlternateId(string $sensorAlternateId): void
        {
            $this->sensorAlternateId = $sensorAlternateId;
        }

        /**
         * @return ConnectionCriteria
         */
        public function getConnectionCriteria(): ConnectionCriteria
        {
            return $this->connectionCriteria;
        }

        /**
         * @param ConnectionCriteria $connectionCriteria
         */
        public function setConnectionCriteria(ConnectionCriteria $connectionCriteria): void
        {
            $this->connectionCriteria = $connectionCriteria;
        }

        /**
         * @return Authentication
         */
        public function getAuthentication(): Authentication
        {
            return $this->authentication;
        }

        /**
         * @param Authentication $authentication
         */
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
                    $classname = __NAMESPACE__ . '\\' .ucfirst($key);
                    $object = new $classname();
                    $this->$key = $object->jsonDeserialize($value);
                } else {
                    try {
                        $this->$key = $value;
                    } catch (Exception $ex){
                        echo $ex;
                    }
                }
            }
            return $this;
        }
    }
}