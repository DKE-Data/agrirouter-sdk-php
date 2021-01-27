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
    class ConnectionCriteria implements JsonSerializable, JsonDeserializable
    {
        private string $gatewayId;
        private string $measures;
        private string $commands;
        private string $host;
        private string $port;
        private string $clientId;

        #[ArrayShape(['clientId' => "string", 'commands' => "string", 'gatewayId' => "string", 'host' => "string", 'measures' => "string", 'port' => "string"])]
        public function jsonSerialize(): array
        {
            return [
                'clientId' => $this->getClientId(),
                'commands' => $this->getCommands(),
                'gatewayId' => $this->getGatewayId(),
                'host' => $this->getHost(),
                'measures' => $this->getMeasures(),
                'port' => $this->getPort()
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
                    case 'clientId':
                        $this->clientId = $fieldValue;
                        break;
                    case 'commands':
                        $this->commands = $fieldValue;
                        break;
                    case 'gatewayId':
                        $this->gatewayId = $fieldValue;
                        break;
                    case 'host':
                        $this->host = $fieldValue;
                        break;
                    case 'measures':
                        $this->measures = $fieldValue;
                        break;
                    case 'port':
                        $this->port = $fieldValue;
                        break;
                    default:
                        throw new JsonException("Unknown field '$fieldName' for class '" . get_class($this) . "'.", ErrorCodes::UNKNOWN_FIELD_IN_JSON_DATA);
                }
            }
            return $this;
        }
    }
}