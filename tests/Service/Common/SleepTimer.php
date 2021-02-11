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
         * @param MqttClient|null $mqttClient
         * @throws DataTransferException
         * @throws MqttClientException
         * @throws ProtocolViolationException
         */
        public static function letTheAgrirouterProcessTheMessage(int $seconds = 3, MqttClient $mqttClient = null)
        {
            if ($mqttClient !== null) {
                $mqttClient->wait($seconds);
            } else {
                sleep($seconds);
            }
        }
    }
}