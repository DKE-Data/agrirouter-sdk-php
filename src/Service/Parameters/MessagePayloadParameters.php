<?php

namespace App\Service\Parameters {


    /**
     * Parameter class for message encoding - in this case the message payload.
     * @package App\Service\Parameters
     */
    class MessagePayloadParameters
    {
        private ?string $typeUrl = null;
        private ?string $value = null;

        public function getValue(): string
        {
            return $this->value;
        }

        public function setValue(string $value): void
        {
            $this->value = $value;
        }

        public function getTypeUrl(): string
        {
            return $this->typeUrl;
        }

        public function setTypeUrl(string $typeUrl): void
        {
            $this->typeUrl = $typeUrl;
        }

    }
}