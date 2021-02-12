<?php declare(strict_types=1);

namespace Lib\Tests\Helper {

    use App\Dto\Onboard\OnboardResponse;
    use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
    use Psr\Log\LoggerInterface;

    /**
     * Manages the PhpMqttClient with Logging.
     * @package Lib\Tests\Helper
     */
    class PhpMqttClientBuilder
    {
        private LoggerInterface $logger;
        private string $host;
        private string $clientId;
        private int $port;
        private OnboardResponse $onboardResponse;

        /**
         * Sets the logger for the mqtt client.
         * @param LoggerInterface $logger The psr compatible logger.
         * @return $this -
         */
        public function withLogger(LoggerInterface $logger): self
        {
            $this->logger = $logger;
            return $this;
        }

        /**
         * Sets the onboard response for the mqtt client.
         * @param OnboardResponse $onboardResponse The onboard response.
         * @return $this -
         */
        public function withOnboardResponse(OnboardResponse $onboardResponse):self
        {
            $this->onboardResponse = $onboardResponse;
            $connectionCriteria = $this->onboardResponse->getConnectionCriteria();
            $this->host = $connectionCriteria->getHost();
            $this->port = (int)$connectionCriteria->getPort();
            $this->clientId = $connectionCriteria->getClientId();

            return $this;
        }

        /**
         * Get the MQTT client.
         * @return MqttClient -
         * @throws ProtocolNotSupportedException
         */
        public function build(): MqttClient
        {

            return new MqttClient(
                new \PhpMqtt\Client\MqttClient(
                    host: $this->host,
                    port: $this->port,
                    clientId: $this->clientId,
                    logger: $this->logger)
                , $this->onboardResponse
                , $this->logger);
        }
    }
}