<?php declare(strict_types=1);

namespace Lib\Tests\Helper {

    use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;

    /**
     * Manages the PhpMqttClient with Logging.
     * @package Lib\Tests\Helper
     */
    class PhpMqttClientBuilder
    {
        private MqttClient $mqttClient;

        /**
         * Constructor.
         * @param string $host
         * @param string $port
         * @param string $clientId
         * @throws ProtocolNotSupportedException
         */
        public function __construct(string $host, string $port, string $clientId)
        {
            $this->mqttClient = new MqttClient(new \PhpMqtt\Client\MqttClient(
                host: $host,
                port: (int)$port,
                clientId: $clientId,
                logger: LoggerBuilder::createConsoleLogger()));
        }

        /**
         * Get the MQTT client.
         * @return MqttClient -
         */
        public function build(): MqttClient
        {
            return $this->mqttClient;
        }
    }
}