<?php

namespace App\Service\Common {

    use Agrirouter\Commons\Messages;
    use Agrirouter\Feed\Response\HeaderQueryResponse;
    use Agrirouter\Feed\Response\MessageQueryResponse;
    use Agrirouter\Response\Payload\Account\ListEndpointsResponse;
    use Agrirouter\Response\ResponseEnvelope;
    use Agrirouter\Response\ResponsePayloadWrapper;
    use App\Api\Exceptions\DecodeMessageException;
    use App\Api\Exceptions\ErrorCodes;
    use App\Dto\Messaging\DecodedMessage;
    use Exception;
    use Google\Protobuf\Any;
    use Google\Protobuf\Internal\CodedInputStream;

    /**
     * Service class to decode messages from the AR.
     * @template-implements DecodeMessagesServiceInterface<Messages>
     * @package App\Service\Messaging
     */
    class DecodeMessageService
    {
        /**
         * Decode a raw Base64 message from the AR.
         * @param string $message The Base64 encoded messages from the AR.
         * @return DecodedMessage .
         * @throws DecodeMessageException .
         */
        public function decodeResponse(string $message): DecodedMessage
        {
            try {
                $inputStream = new CodedInputStream(base64_decode($message));

                $size = 0;
                $inputStream->readVarint32($size);
                $inputStream->readRaw($size, $responseEnvelopeBuffer);
                $responseEnvelope = new ResponseEnvelope();
                $responseEnvelope->mergeFromString($responseEnvelopeBuffer);

                $inputStream->readVarint32($size);
                $inputStream->readRaw($size, $responsePayloadWrapperBuffer);
                $responsePayloadWrapper = new ResponsePayloadWrapper();
                $responsePayloadWrapper->mergeFromString($responsePayloadWrapperBuffer);

                $decodedMessage = new DecodedMessage();
                $decodedMessage->setResponseEnvelope($responseEnvelope);
                $decodedMessage->setResponsePayloadWrapper($responsePayloadWrapper);
                return $decodedMessage;
            } catch (Exception $e) {
                throw new DecodeMessageException("Could not decode the messages from the AR.", ErrorCodes::COULD_NOT_DECODE_MESSAGE, $e);
            }
        }

        /**
         * Decode message details.
         * @param Any $details .
         * @return mixed Depends on the type URL of the details.
         * @throws DecodeMessageException .
         * @throws Exception
         * @noinspection PhpMixedReturnTypeCanBeReducedInspection
         */
        public function decodeDetails(Any $details): mixed
        {
            switch ($details->getTypeUrl()) {
                case TypeUrlService::getTypeUrl(Messages::class):
                    $messages = new Messages();
                    $messages->mergeFromString($details->getValue());
                    return $messages;
                case TypeUrlService::getTypeUrl(ListEndpointsResponse::class):
                    $listEndpointsResponse = new ListEndpointsResponse();
                    $listEndpointsResponse->mergeFromString($details->getValue());
                    return $listEndpointsResponse;
                case TypeUrlService::getTypeUrl(HeaderQueryResponse::class):
                    $headerQueryResponse = new HeaderQueryResponse();
                    $headerQueryResponse->mergeFromString($details->getValue());
                    return $headerQueryResponse;
                case TypeUrlService::getTypeUrl(MessageQueryResponse::class):
                    $messageQueryResponse = new MessageQueryResponse();
                    $messageQueryResponse->mergeFromString($details->getValue());
                    return $messageQueryResponse;
                default:
                    throw new DecodeMessageException("Could not handle type '" . $details->getTypeUrl() . "' while decoding details.", ErrorCodes::COULD_NOT_DECODE_DETAILS);
            }
        }

    }
}