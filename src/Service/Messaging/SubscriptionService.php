<?php declare(strict_types=1);

namespace App\Service\Messaging {

    use Agrirouter\Request\Payload\Endpoint\Subscription;
    use Agrirouter\Request\RequestEnvelope\Mode;
    use App\Api\Service\Messaging\EncodeMessageServiceInterface;
    use App\Api\Service\Messaging\MessagingServiceInterface;
    use App\Api\Service\Parameters\MessagingParameters;
    use App\Definitions\TechnicalMessageTypeDefinitions;
    use App\Dto\Messaging\EncodedMessage;
    use App\Dto\Messaging\MessagingResult;
    use App\Service\Common\EncodeMessageService;
    use App\Service\Common\TypeUrlService;
    use App\Service\Common\UuidService;
    use App\Service\Parameters\MessageHeaderParameters;
    use App\Service\Parameters\MessagePayloadParameters;
    use App\Service\Parameters\SubscriptionParameters;
    use JetBrains\PhpStorm\Pure;

    /**
     * Service to send the subscriptions to the AR.
     * @template-implements MessagingServiceInterface<CapabilityParameters>
     * @template-implements EncodeMessageServiceInterface<CapabilityParameters>
     * @package App\Service\Messaging
     */
    class SubscriptionService implements MessagingServiceInterface, EncodeMessageServiceInterface
    {

        private MessagingServiceInterface $messagingService;

        /**
         * Constructor.
         * @param MessagingServiceInterface $messagingService Service for message sending.
         */
        #[Pure] public function __construct(MessagingServiceInterface $messagingService)
        {
            $this->messagingService = $messagingService;
        }

        /**
         * Encoding of the message.
         * @param SubscriptionParameters $parameters .
         * @return EncodedMessage .
         */
        public function encode($parameters): EncodedMessage
        {
            $messageHeaderParameters = new MessageHeaderParameters();
            $messageHeaderParameters->setApplicationMessageId($parameters->getApplicationMessageId());
            $messageHeaderParameters->setApplicationMessageSeqNo($parameters->getApplicationMessageSeqNo());
            $messageHeaderParameters->setTeamSetContextId($parameters->getTeamSetContextId());
            $messageHeaderParameters->setMode(Mode::DIRECT);
            $messageHeaderParameters->setTechnicalMessageType(TechnicalMessageTypeDefinitions::DKE_SUBSCRIPTION);

            $subscription = new Subscription();

            $messagePayloadParameters = new MessagePayloadParameters();
            $messagePayloadParameters->setTypeUrl(TypeUrlService::getTypeUrl(Subscription::class));
            $messagePayloadParameters->setValue($subscription->serializeToString());

            $encodeMessageService = new EncodeMessageService();
            $messageContent = $encodeMessageService->encode($messageHeaderParameters, $messagePayloadParameters);

            $encodedMessage = new EncodedMessage();
            $encodedMessage->setId(UuidService::newUuid());
            $encodedMessage->setContent($messageContent);
            return $encodedMessage;
        }

        /**
         * Send message.
         * @param SubscriptionParameters $parameters .
         * @return MessagingResult .
         */
        public function send($parameters): MessagingResult
        {
            $messagingParameters = new MessagingParameters();
            $messagingParameters->setOnboardResponse($parameters->getOnboardResponse());
            $messagingParameters->setApplicationMessageId($parameters->getApplicationMessageId());
            $messagingParameters->setApplicationMessageSeqNo($parameters->getApplicationMessageSeqNo());
            $encodedMessages = $this->encode($parameters);
            $messagingParameters->setEncodedMessages([$encodedMessages->getContent()]);
            return $this->messagingService->send($messagingParameters);
        }

    }
}