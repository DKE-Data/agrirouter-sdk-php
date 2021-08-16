<?php

namespace App\Dto\Messaging {

    /**
     * Result after encoding a message.
     * @package App\Dto\Messaging
     */
    class EncodedMessage
    {

        private ?string $id = null;
        private ?string $content = null;

        public function getId(): string
        {
            return $this->id;
        }

        public function setId(string $id): void
        {
            $this->id = $id;
        }

        public function getContent(): string
        {
            return $this->content;
        }

        public function setContent(string $content): void
        {
            $this->content = $content;
        }

    }
}