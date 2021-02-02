<?php declare(strict_types=1);

namespace App\Service\Common {

    use Agrirouter\Request\RequestEnvelope;
    use Agrirouter\Request\RequestPayloadWrapper;
    use App\Service\Parameters\MessageHeaderParameters;
    use App\Service\Parameters\MessagePayloadParameters;
    use Google\Protobuf\Any;
    use Google\Protobuf\Internal\CodedOutputStream;

    class EncodeMessageService
    {
        /**
         * Encode a message that will be sent to the agrirouter.
         * @param MessageHeaderParameters $messageHeaderParameters Parameters for the message header.
         * @param MessagePayloadParameters $messagePayloadParameters Parameters for the message payload.
         * @return string A base 64 encoded message.
         */
        public function encode(MessageHeaderParameters $messageHeaderParameters, MessagePayloadParameters $messagePayloadParameters): string
        {
            $requestEnvelope = $this->encodeHeader($messageHeaderParameters);
            $requestPayloadWrapper = $this->encodePayload($messagePayloadParameters);

            $f = CodedOutputStream::MAX_VARINT64_BYTES + $requestEnvelope->byteSize() + CodedOutputStream::MAX_VARINT64_BYTES + $requestPayloadWrapper->byteSize();

            $codedOutputStream = new CodedOutputStream($f);
            $codedOutputStream->writeVarint32($requestEnvelope->byteSize(), false);
            $requestEnvelope->serializeToStream($codedOutputStream);
            $codedOutputStream->writeVarint32($requestPayloadWrapper->byteSize(), false);
            $requestPayloadWrapper->serializeToStream($codedOutputStream);

            $rawData = $codedOutputStream->getData();
            return base64_encode($rawData);
        }

        private function encodeHeader(MessageHeaderParameters $messageHeaderParameters): RequestEnvelope
        {
            $requestEnvelope = new RequestEnvelope();
            $requestEnvelope->setApplicationMessageId(empty($messageHeaderParameters->getApplicationMessageId()) ? UuidService::newUuid() : $messageHeaderParameters->getApplicationMessageId());
            $requestEnvelope->setApplicationMessageSeqNo($messageHeaderParameters->getApplicationMessageSeqNo());
            $requestEnvelope->setTechnicalMessageType($messageHeaderParameters->getTechnicalMessageType());
            $requestEnvelope->setMode($messageHeaderParameters->getMode());
            $requestEnvelope->setTimestamp(UtcDataService::nowAsTimestamp());
            return $requestEnvelope;
        }

        private function encodePayload(MessagePayloadParameters $messagePayloadParameters): RequestPayloadWrapper
        {
            $requestPayloadWrapper = new RequestPayloadWrapper();
            $any = new Any();
            $any->setTypeUrl($messagePayloadParameters->getTypeUrl());
            $any->setValue($messagePayloadParameters->getValue());
            $requestPayloadWrapper->setDetails($any);
            return $requestPayloadWrapper;
        }
    }
}