<?php

namespace App\Service\Common {

    use App\Api\Service\Messaging\MessagingServiceInterface;
    use App\Dto\Messaging\Inner\Message;
    use App\Dto\Requests\MessageRequest;

    /**
     * Abstract service to send messages to the AR.
     * @package App\Service\Common
     * @template-implements MessagingServiceInterface<MessagingParameters>
     */
    abstract class AbstractMessagingService implements MessagingServiceInterface
    {
        /**
         * Creates the message request for the agrirouter.
         * @param $parameters - The messaging parameters.
         * @return MessageRequest -
         */
        public function createMessageRequest($parameters): MessageRequest
        {
            $messageRequest = new MessageRequest();
            $messageRequest->setSensorAlternateId($parameters->getOnboardResponse()->getSensorAlternateId());
            $messageRequest->setCapabilityAlternateId($parameters->getOnboardResponse()->getCapabilityAlternateId());

            $messages = [];
            foreach ($parameters->getEncodedMessages() as $encodedMessage) {
                $message = new Message();
                $message->setContent($encodedMessage);
                $message->setTimestamp(UtcDataService::nowAsUnixTimestamp());
                array_push($messages, $message);
            }
            $messageRequest->setMessages($messages);
            return $messageRequest;
        }
    }
}