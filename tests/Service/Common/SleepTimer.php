<?php

namespace Lib\Tests\Service\Common {

    use Lib\Tests\Helper\MqttClient;
    use PhpMqtt\Client\Exceptions\DataTransferException;
    use PhpMqtt\Client\Exceptions\MqttClientException;
    use PhpMqtt\Client\Exceptions\ProtocolViolationException;

    /**
     * Timer.
     * @package Lib\Tests\Service\Common
     */
    class SleepTimer
    {

        /**
         * Sleep for a dedicated time and let the AR process the message.
         * @param int $seconds Seconds to sleep.
         */
        public static function letTheAgrirouterProcessTheMessage(int $seconds = 3)
        {
            sleep($seconds);
        }

        /**
         * Sleep for a dedicated time and let the AR process the mqtt message.
         * @param int $seconds Seconds to sleep.
         * @param MqttClient|null $mqttClient
         * @return bool Was the loop interrupted by a callback handler.
         * @throws DataTransferException -
         * @throws MqttClientException -
         * @throws ProtocolViolationException -
         */
        public static function letTheAgrirouterProcessTheMqttMessage(int $seconds = 3, MqttClient $mqttClient = null): bool
        {
            return $mqttClient->wait($seconds);
        }
    }
}