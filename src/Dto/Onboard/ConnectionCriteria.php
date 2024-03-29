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
    class ConnectionCriteria implements JsonSerializable, JsonDeserializableInterface
    {
        private const CLIENT_ID = 'clientId';
        private const COMMANDS = 'commands';
        private const GATEWAY_ID = 'gatewayId';
        private const HOST = 'host';
        private const MEASURES = 'measures';
        private const PORT = 'port';

        private ?string $gatewayId = null;
        private ?string $measures = null;
        private ?string $commands = null;
        private ?string $host = null;
        private ?string $port = null;
        private ?string $clientId = null;

        #[ArrayShape([self::CLIENT_ID => "string", self::COMMANDS => "string", self::GATEWAY_ID => "string", self::HOST => "string", self::MEASURES => "string", self::PORT => "string"])]
        public function jsonSerialize(): array
        {
            return [
                self::CLIENT_ID => $this->getClientId(),
                self::COMMANDS => $this->getCommands(),
                self::GATEWAY_ID => $this->getGatewayId(),
                self::HOST => $this->getHost(),
                self::MEASURES => $this->getMeasures(),
                self::PORT => $this->getPort()
            ];
        }

        public function getClientId(): string
        {
            return $this->clientId;
        }

        public function setClientId(string $clientId): void
        {
            $this->clientId = $clientId;
        }

        public function getCommands(): string
        {
            return $this->commands;
        }

        public function setCommands(string $commands): void
        {
            $this->commands = $commands;
        }

        public function getGatewayId(): string
        {
            return $this->gatewayId;
        }

        public function setGatewayId(string $gatewayId): void
        {
            $this->gatewayId = $gatewayId;
        }

        public function getHost(): string
        {
            return $this->host;
        }

        public function setHost(string $host): void
        {
            $this->host = $host;
        }


        public function getMeasures(): string
        {
            return $this->measures;
        }

        public function setMeasures(string $measures): void
        {
            $this->measures = $measures;
        }

        public function getPort(): string
        {
            return $this->port;
        }

        public function setPort(string $port): void
        {
            $this->port = $port;
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
                    case self::CLIENT_ID:
                        $this->clientId = $fieldValue;
                        break;
                    case self::COMMANDS:
                        $this->commands = $fieldValue;
                        break;
                    case self::GATEWAY_ID:
                        $this->gatewayId = $fieldValue;
                        break;
                    case self::HOST:
                        $this->host = $fieldValue;
                        break;
                    case self::MEASURES:
                        $this->measures = $fieldValue;
                        break;
                    case self::PORT:
                        if (is_int($fieldValue)) {
                            $this->port = "" . $fieldValue;
                        } else {
                            $this->port = $fieldValue;
                        }
                        break;
                    default:
                        throw new JsonException("Unknown field '$fieldName' for class '" . get_class($this) . "'.", ErrorCodes::UNKNOWN_FIELD_IN_JSON_DATA);
                }
            }
            return $this;
        }
    }
}