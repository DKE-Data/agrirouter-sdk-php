<?php declare(strict_types=1);

namespace App\Service\Cloud {

    use Agrirouter\Cloud\Registration\OffboardingRequest;
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
    use App\Service\Parameters\CloudOffboardParameters;
    use App\Service\Parameters\MessageHeaderParameters;
    use App\Service\Parameters\MessagePayloadParameters;

    /**
     * Service to send the cloud onboard message to the AR.
     * @template-implements MessagingServiceInterface<CloudOffboardParametersZ>
     * @template-implements EncodeMessageServiceInterface<CloudOffboardParametersZ>
     * @package App\Service\Messaging
     */
    class CloudOffboardService implements MessagingServiceInterface, EncodeMessageServiceInterface
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
         * @param CloudOffboardParameters $parameters .
         * @return EncodedMessage .
         * @noinspection PhpMissingParamTypeInspection
         */
        public function encode($parameters): EncodedMessage
        {
            $messageHeaderParameters = new MessageHeaderParameters();
            $messageHeaderParameters->setApplicationMessageId($parameters->getApplicationMessageId());
            $messageHeaderParameters->setApplicationMessageSeqNo($parameters->getApplicationMessageSeqNo());
            $messageHeaderParameters->setTeamSetContextId($parameters->getTeamSetContextId());
            $messageHeaderParameters->setMode(Mode::DIRECT);
            $messageHeaderParameters->setTechnicalMessageType(TechnicalMessageTypeDefinitions::CLOUD_OFFBOARD_ENDPOINTS);

            $offboardingRequest = new OffboardingRequest();
            $offboardingRequest->setEndpoints($parameters->getEndpoints());

            $messagePayloadParameters = new MessagePayloadParameters();
            $messagePayloadParameters->setTypeUrl(TypeUrlService::getTypeUrl(OffboardingRequest::class));
            $messagePayloadParameters->setValue($offboardingRequest->serializeToString());

            $encodeMessageService = new EncodeMessageService();
            $messageContent = $encodeMessageService->encode($messageHeaderParameters, $messagePayloadParameters);

            $encodedMessage = new EncodedMessage();
            $encodedMessage->setId(UuidService::newUuid());
            $encodedMessage->setContent($messageContent);
            return $encodedMessage;
        }

        /**
         * Send message.
         * @param CloudOffboardParameters $parameters .
         * @return MessagingResult .
         * @noinspection PhpMissingParamTypeInspection
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