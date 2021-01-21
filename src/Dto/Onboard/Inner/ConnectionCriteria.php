<?php declare(strict_types=1);

namespace App\Dto\Onboard\Inner {


    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Class ConnectionCriteria - Data transfer object for the communication.
     * @package App\Dto\Onboard\Inner
     */
    class ConnectionCriteria implements JsonSerializable
    {
        public string $gatewayId;
        public string $measures;
        public string $commands;
        public string $host;
        public string $port;
        public string $clientId;

        public function getGatewayId(): string
        {
            return $this->gatewayId;
        }

        public function setGatewayId(string $gatewayId): void
        {
            $this->gatewayId = $gatewayId;
        }

        public function getMeasures(): string
        {
            return $this->measures;
        }

        public function setMeasures(string $measures): void
        {
            $this->measures = $measures;
        }

        public function getCommands(): string
        {
            return $this->commands;
        }

        public function setCommands(string $commands): void
        {
            $this->commands = $commands;
        }

        public function getHost(): string
        {
            return $this->host;
        }

        public function setHost(string $host): void
        {
            $this->host = $host;
        }

        public function getPort(): string
        {
            return $this->port;
        }

        public function setPort(string $port): void
        {
            $this->port = $port;
        }

        public function getClientId(): string
        {
            return $this->clientId;
        }

        public function setClientId(string $clientId): void
        {
            $this->clientId = $clientId;
        }

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

        /**
         * Creates an object of type ConnectionCriteria from a given data array
         * @param array $data
         * @return ConnectionCriteria
         */
        public static function createFromArray(array $data): self
        {
            $connectionCriteria = new self();
            foreach ($data as $key => $value) {

                $setterToCall = "set" . ucfirst($key);
                $connectionCriteria->$setterToCall($value);

            }
            return $connectionCriteria;
        }
    }
}