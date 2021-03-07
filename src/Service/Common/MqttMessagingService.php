<?php declare(strict_types=1);

namespace App\Service\Common {

    use App\Api\Messaging\MqttClientInterface;
    use App\Api\Service\Parameters\MessagingParameters;
    use App\Dto\Messaging\MessagingResult;

    /**
     * Service to send messages to the AR via MQTT.
     * @package App\Service\Common
     * @template-implements MessagingServiceInterface<MessagingParameters>
     */
    class MqttMessagingService extends AbstractMessagingService
    {
        private MqttClientInterface $mqttClient;

        /**
         * Constructor.
         * @param MqttClientInterface $mqttClient -
         */
        public function __construct(MqttClientInterface $mqttClient)
        {
            $this->mqttClient = $mqttClient;
        }

        /**
         * Send message to the AR using the given message parameters.
         * @param MessagingParameters $parameters Messaging parameters.
         * @return MessagingResult -
         * @noinspection PhpMissingParamTypeInspection
         */
        public function send($parameters): MessagingResult
        {
            $mqttPayload = json_encode($this->createMessageRequest($parameters));
            $this->mqttClient->publish($parameters->getOnboardResponse()->getConnectionCriteria()->getMeasures(), $mqttPayload);
            $messagingResult = new MessagingResult();
            $messageIds = [];
            array_push($messageIds, $parameters->getApplicationMessageId());
            $messagingResult->setMessageIds($messageIds);
            return $messagingResult;
        }
    }
}