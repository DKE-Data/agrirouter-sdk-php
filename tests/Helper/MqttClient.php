<?php declare(strict_types=1);

namespace Lib\Tests\Helper {

    use App\Api\Messaging\MqttClientInterface;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Common\CertificateService;
    use JetBrains\PhpStorm\Pure;
    use PhpMqtt\Client\ConnectionSettings;
    use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
    use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
    use PhpMqtt\Client\Exceptions\DataTransferException;
    use PhpMqtt\Client\Exceptions\MqttClientException;
    use PhpMqtt\Client\Exceptions\ProtocolViolationException;
    use PhpMqtt\Client\Exceptions\RepositoryException;
    use PhpMqtt\Client\MqttClient as PhpMqttClient;
    use Psr\Log\LoggerInterface;

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
        private ?LoggerInterface $logger;

        /**
         * Constructor.
         * @param PhpMqttClient $mqttClient The PhpMqtt client.
         * @param LoggerInterface|null $logger The logger used for logging.
         */
        #[Pure] public function __construct(PhpMqttClient $mqttClient, ?LoggerInterface $logger = null)
        {
            $this->mqttClient = $mqttClient;
            $this->logger = $logger;
        }

        /**
         * @inheritDoc
         * @throws ConfigurationInvalidException .
         * @throws ConnectingToBrokerFailedException .
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
         * @throws DataTransferException .
         */
        public function disconnect(): void
        {
            $this->mqttClient->disconnect();
        }

        /**
         * @inheritDoc
         * @throws DataTransferException .
         * @throws RepositoryException .
         */
        public function subscribe(string $topic, callable $callback = null, int $qualityOfService = 2): void
        {
            $this->mqttClient->subscribe($topic, $callback, $qualityOfService);
        }

        /**
         * @inheritDoc
         * @throws DataTransferException .
         * @throws RepositoryException .
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
         * @throws DataTransferException .
         * @throws RepositoryException .
         */
        public function publish(string $topic, string $message, int $qualityOfService = 0, bool $retain = false): void
        {
            $this->mqttClient->publish($topic, $message, $qualityOfService, $retain);
        }

        /**
         * Interrupts the waiting loop of the client.
         */
        public function interrupt(): void
        {
            $this->mqttClient->interrupt();
        }

        /**
         * Waits for a given time period until a new message arrives on the topic of the client.
         * Can be interrupted with a call of the interrupt() method of the client.
         * @param int $seconds Time to wait for messages in the outbox.
         * @return bool Indicates whether the loop has been interrupted by some other handler.
         * @throws DataTransferException .
         * @throws MqttClientException .
         * @throws ProtocolViolationException .
         */
        public function wait(int $seconds = 5): bool
        {
            $maxRuntime = (float)$seconds;
            $wasInterrupted = true;

            $this->mqttClient->registerLoopEventHandler($this->getLoopEventHandler($maxRuntime, $this->logger, $wasInterrupted));
            $this->mqttClient->loop(true);
            $this->mqttClient->unregisterLoopEventHandler();
            return $wasInterrupted;
        }

        /**
         * Creates a callback function for the php mqtt client loop event handler. Normally the loop runs endless if not interrupted.
         * This callback interrupts the loop after the given amount of seconds.
         * @param float $maxRuntime Maximum time to wait in seconds.
         * @param LoggerInterface $logger The psr compatible logger.
         * @param bool $wasInterrupted Indicates whether the loop has been interrupted by some other handler.
         * @return callable - The closure for the LoopEventHandler of the php mqtt client.
         */
        private function getLoopEventHandler(float &$maxRuntime, LoggerInterface &$logger, bool &$wasInterrupted): callable
        {
            return function (PhpMqttClient $client, float $elapsedTime) use (&$maxRuntime, &$logger, &$wasInterrupted) {
                $wasInterrupted = true;
                if ($elapsedTime >= $maxRuntime) {
                    $logger->info("No Interrupt in loop received. Runtime: " . $elapsedTime);
                    $wasInterrupted = false;
                    $client->interrupt();
                    return;
                }
            };
        }
    }
}