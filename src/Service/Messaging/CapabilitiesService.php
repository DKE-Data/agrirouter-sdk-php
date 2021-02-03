<?php declare(strict_types=1);

namespace App\Service\Messaging {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification;
    use Agrirouter\Request\RequestEnvelope\Mode;
    use App\Api\Messaging\MessagingServiceInterface;
    use App\Api\Service\Messaging\CapabilitiesServiceInterface;
    use App\Api\Service\Messaging\EncodeMessageServiceInterface;
    use App\Api\Service\Parameters\MessagingParameters;
    use App\Definitions\TechnicalMessageTypeDefinitions;
    use App\Dto\Messaging\EncodedMessage;
    use App\Dto\Messaging\MessagingResult;
    use App\Service\Common\EncodeMessageService;
    use App\Service\Common\TypeUrlService;
    use App\Service\Common\UuidService;
    use App\Service\Parameters\CapabilityParameters;
    use App\Service\Parameters\MessageHeaderParameters;
    use App\Service\Parameters\MessagePayloadParameters;

    /**
     * Service to send the capabilities to the AR.
     * @template-implements MessagingServiceInterface<CapabilityParameters>
     * @template-implements EncodeMessageServiceInterface<CapabilityParameters>
     * @package App\Service\Messaging
     */
    class CapabilitiesService implements CapabilitiesServiceInterface, EncodeMessageServiceInterface
    {

        private MessagingServiceInterface $messagingService;

        /**
         * Constructor.
         * @param MessagingServiceInterface $messagingService Service for message sending.
         */
        public function __construct(MessagingServiceInterface $messagingService)
        {
            $this->messagingService = $messagingService;
        }

        /**
         * Encoding of the message.
         * @param CapabilityParameters $parameters -
         * @return EncodedMessage -
         */
        public function encode($parameters): EncodedMessage
        {
            $messageHeaderParameters = new MessageHeaderParameters();
            $messageHeaderParameters->setApplicationMessageId($parameters->getApplicationMessageId());
            $messageHeaderParameters->setApplicationMessageSeqNo($parameters->getApplicationMessageSeqNo());
            $messageHeaderParameters->setTeamSetContextId($parameters->getTeamSetContextId());
            $messageHeaderParameters->setMode(Mode::DIRECT);
            $messageHeaderParameters->setTechnicalMessageType(TechnicalMessageTypeDefinitions::DKE_CAPABILITIES);

            $capabilitySpecification = new CapabilitySpecification();
            $capabilitySpecification->setAppCertificationId($parameters->getApplicationId());
            $capabilitySpecification->setAppCertificationVersionId($parameters->getCertificationVersionId());
            $capabilitySpecification->setEnablePushNotifications($parameters->getEnablePushNotification());
            if (!count($parameters->getCapabilityParameters()) == 0) {
                $capabilitySpecification->setCapabilities($parameters->getCapabilityParameters());
            }

            $messagePayloadParameters = new MessagePayloadParameters();
            $messagePayloadParameters->setTypeUrl(TypeUrlService::getTypeUrl(CapabilitySpecification::class));
            $messagePayloadParameters->setValue($capabilitySpecification->serializeToString());

            $encodeMessageService = new EncodeMessageService();
            $messageContent = $encodeMessageService->encode($messageHeaderParameters, $messagePayloadParameters);

            $encodedMessage = new EncodedMessage();
            $encodedMessage->setId(UuidService::newUuid());
            $encodedMessage->setContent($messageContent);
            return $encodedMessage;
        }

        /**
         * Send message.
         * @param CapabilityParameters $parameters -
         * @return MessagingResult -
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