<?php declare(strict_types=1);

namespace App\Service\Messaging {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification;
    use Agrirouter\Request\RequestEnvelope\Mode;
    use App\Api\Common\MessagingServiceInterface;
    use App\Api\Service\Messaging\CapabilitiesServiceInterface;
    use App\Api\Service\Messaging\EncodeMessageServiceInterface;
    use App\Definitions\TechnicalMessageTypeDefinitions;
    use App\Dto\Messaging\EncodedMessage;
    use App\Dto\Messaging\MessagingResult;
    use App\Service\Common\EncodeMessageService;
    use App\Service\Common\TypeUrlService;
    use App\Service\Common\UuidService;
    use App\Service\Parameters\CapabilityParameters;
    use App\Service\Parameters\MessageHeaderParameters;
    use App\Service\Parameters\MessagePayloadParameters;
    use JetBrains\PhpStorm\Pure;

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
         * @param CapabilityParameters $parameters
         * @return EncodedMessage
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

        #[Pure] public function send($parameters): MessagingResult
        {
            return new MessagingResult();
        }
    }
}