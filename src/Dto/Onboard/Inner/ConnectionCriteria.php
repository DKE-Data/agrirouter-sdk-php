<?php declare(strict_types=1);

namespace App\Dto\Onboard\Inner {


    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    class ConnectionCriteria implements JsonSerializable
    {
        public string $gatewayId;
        public string $measures;
        public string $commands;
        public string $host;
        public string $port;
        public string $clientId;

        /**
         * @return string
         */
        public function getGatewayId(): string
        {
            return $this->gatewayId;
        }

        /**
         * @param string $gatewayId
         */
        public function setGatewayId(string $gatewayId): void
        {
            $this->gatewayId = $gatewayId;
        }

        /**
         * @return string
         */
        public function getMeasures(): string
        {
            return $this->measures;
        }

        /**
         * @param string $measures
         */
        public function setMeasures(string $measures): void
        {
            $this->measures = $measures;
        }

        /**
         * @return string
         */
        public function getCommands(): string
        {
            return $this->commands;
        }

        /**
         * @param string $commands
         */
        public function setCommands(string $commands): void
        {
            $this->commands = $commands;
        }

        /**
         * @return string
         */
        public function getHost(): string
        {
            return $this->host;
        }

        /**
         * @param string $host
         */
        public function setHost(string $host): void
        {
            $this->host = $host;
        }

        /**
         * @return string
         */
        public function getPort(): string
        {
            return $this->port;
        }

        /**
         * @param string $port
         */
        public function setPort(string $port): void
        {
            $this->port = $port;
        }

        /**
         * @return string
         */
        public function getClientId(): string
        {
            return $this->clientId;
        }

        /**
         * @param string $clientId
         */
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