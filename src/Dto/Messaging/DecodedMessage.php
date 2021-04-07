<?php

namespace App\Dto\Messaging {

    use Agrirouter\Response\ResponseEnvelope;
    use Agrirouter\Response\ResponsePayloadWrapper;

    /**
     * Decoded message, contains the response envelope and payload.
     * @package App\Dto\Messaging
     */
    class DecodedMessage
    {
        private ?ResponseEnvelope $responseEnvelope = null;
        private ?ResponsePayloadWrapper $responsePayloadWrapper = null;

        public function getResponseEnvelope(): ResponseEnvelope
        {
            return $this->responseEnvelope;
        }

        public function setResponseEnvelope(ResponseEnvelope $responseEnvelope): void
        {
            $this->responseEnvelope = $responseEnvelope;
        }

        public function getResponsePayloadWrapper(): ResponsePayloadWrapper
        {
            return $this->responsePayloadWrapper;
        }

        public function setResponsePayloadWrapper(ResponsePayloadWrapper $responsePayloadWrapper): void
        {
            $this->responsePayloadWrapper = $responsePayloadWrapper;
        }

    }
}