<?php declare(strict_types=1);

namespace App\Api\Messaging {

    use App\Dto\Onboard\OnboardResponse;

    /**
     * Interface to handle external MQTT client implementations.
     * @package App\Api\Common
     */
    interface MqttClientInterface
    {
        public const MQTT_CONNECT_TIMEOUT = 20;
        public const MQTT_SOCKET_TIMEOUT = 20;
        public const MQTT_USE_TLS = true;
        public const MQTT_USE_CLEAN_SESSION = true;
        public const MQTT_KEEP_ALIVE_INTERVAL = 60;

        /**
         * Connects the client to the agrirouter mqtt broker.
         * @param OnboardResponse $onboardResponse The onboard response with the necessary connection parameters.
         */
        public function connect(OnboardResponse $onboardResponse): void;

        /**
         * Disconnects the client to the agrirouter mqtt broker.
         */
        public function disconnect(): void;

        /**
         * Subscribes a client to an agrirouter topic
         * @param string $topic The topic name to subscribe to.
         * @param callable|null $callback Callback function for receiving messages if supported bei the client implementation, null otherwise.
         * @param int $qualityOfService The qos parameter for the message handling.
         */
        public function subscribe(string $topic, callable $callback = null, int $qualityOfService = 2): void;

        /**
         * Unsubscribe from an agrirouter topic.
         * @param string $topic The topic name to subscribe to.
         */
        public function unsubscribe(string $topic): void;

        /**
         * Checks if the client is connected to the agrirouter.
         * @return bool True if the client is connected, false otherwise.
         */
        public function isConnected(): bool;

        /**
         * Publishes a message on a given topic within the agrirouter.
         * @param string $topic The topic name.
         * @param string $message The message.
         * @param int $qualityOfService The qos parameter for the message handling.
         * @param bool $retain True if the broker should retain the last message and deliver it to new connected clients.
         */
        public function publish(string $topic, string $message, int $qualityOfService = 2, bool $retain = false): void;
    }
}