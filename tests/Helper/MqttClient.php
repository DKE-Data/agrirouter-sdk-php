<?php declare(strict_types=1);

namespace Lib\Tests\Helper {

    use App\Api\Messaging\MqttClientInterface;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Common\CertificateService;
    use App\Service\Common\DecodeMessageService;
    use JetBrains\PhpStorm\Pure;
    use Monolog\Logger;
    use PhpMqtt\Client\ConnectionSettings;
    use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
    use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
    use PhpMqtt\Client\Exceptions\DataTransferException;
    use PhpMqtt\Client\Exceptions\MqttClientException;
    use PhpMqtt\Client\Exceptions\ProtocolViolationException;
    use PhpMqtt\Client\Exceptions\RepositoryException;
    use PhpMqtt\Client\MqttClient as PhpMqttClient;

    /**
     * MQTT client implementation to wrap the PhpMqtt client.
     * @package Lib\Tests\Helper
     */
    class MqttClient implements MqttClientInterface
    {
        public const MQTT_CONNECT_TIMEOUT = 20;
        public const MQTT_SOCKET_TIMEOUT = 20;
        public const MQTT_USE_TLS = true;
        public const MQTT_USE_CLEAN_SESSION = true;
        public const MQTT_KEEP_ALIVE_INTERVAL = 60;

        private PhpMqttClient $mqttClient;
        private ?Logger $logger;

        /**
         * Constructor.
         * @param PhpMqttClient $mqttClient The PhpMqtt client.
         * @param Logger|null $logger
         */
        public function __construct(PhpMqttClient $mqttClient, ?Logger $logger = null)
        {
            $this->mqttClient = $mqttClient;
            $this->logger = $logger;
        }

        /**
         * @inheritDoc
         * @throws ConfigurationInvalidException
         * @throws ConnectingToBrokerFailedException
         */
        public function connect(OnboardResponse $onboardResponse): void
        {
            $clientCertPath = CertificateService::createCertificateFile($onboardResponse);
            $mqttConnectionSettings = (new ConnectionSettings())
                ->setConnectTimeout(self::MQTT_CONNECT_TIMEOUT)
                ->setSocketTimeout(self::MQTT_SOCKET_TIMEOUT)
                ->setUseTls(self::MQTT_USE_TLS)
                ->setKeepAliveInterval(self::MQTT_KEEP_ALIVE_INTERVAL)
                ->setTlsClientCertificateFile($clientCertPath)
                ->setTlsClientCertificateKeyFile($clientCertPath)
                ->setTlsClientCertificateKeyPassphrase($onboardResponse->getAuthentication()->getSecret());
            $this->mqttClient->connect($mqttConnectionSettings, self::MQTT_USE_CLEAN_SESSION);
        }

        /**
         * @inheritDoc
         * @throws DataTransferException
         */
        public function disconnect(): void
        {
            $this->mqttClient->disconnect();
        }

        /**
         * @inheritDoc
         * @throws DataTransferException
         * @throws RepositoryException
         */
        public function subscribe(string $topic, callable $callback = null, int $qualityOfService = 0): void
        {
            $this->mqttClient->subscribe($topic, $callback, $qualityOfService = 0);
        }

        /**
         * @inheritDoc
         * @throws DataTransferException
         * @throws RepositoryException
         */
        public function unsubscribe(string $topic): void
        {
            $this->mqttClient->unsubscribe($topic);
        }

        /**
         * @inheritDoc
         */
        #[Pure] public function isConnected(): bool
        {
            return $this->mqttClient->isConnected();
        }

        /**
         * @inheritDoc
         * @throws DataTransferException
         * @throws RepositoryException
         */
        public function publish(string $topic, string $message, int $qualityOfService = 0, bool $retain = false): void
        {
            $this->mqttClient->publish($topic, $message, $qualityOfService, $retain);
        }

        /**
         * Waits until a new message arrives on the topic of the client.
         * @throws DataTransferException -
         * @throws MqttClientException -
         * @throws ProtocolViolationException -
         */
        public function wait(): void
        {
            $this->mqttClient->loop();
        }

        /**
         * Returns the message handler for php mqtt.
         * @param $logger Logger A logger for the handler.
         * @param $receivedDecodedMessage - Object reference store for the received messages.
         * @return callable The callable handler.
         */
        public function getHandler(Logger &$logger, &$receivedDecodedMessage): callable
        {
            return function (PhpMqttClient $client, string $topic, string $message)
            use (&$logger, &$receivedDecodedMessage) {
                $logger->info("We received a message on topic [$topic]: $message");
                $decodedMessage = json_decode($message, true);
                $command = $decodedMessage['command'];
                $decodeMessageService = new DecodeMessageService();
                $receivedDecodedMessage = $decodeMessageService->decodeResponse($command['message']);

                $client->interrupt();
            };
        }

        /**
         * Registers the message received callback method for the php mqtt client.
         * @param callable $handler A callback method.
         */
        public function registerMessageReceivedEventHandler(callable $handler): void
        {
            $this->mqttClient->registerMessageReceivedEventHandler($handler);
        }
    }
}