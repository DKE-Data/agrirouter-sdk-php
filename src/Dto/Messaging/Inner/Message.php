<?php

namespace App\Dto\Messaging\Inner {

    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Data transfer object for the communication.
     * @package App\Dto\Messaging\Inner
     */
    class Message implements JsonSerializable
    {
        private const MESSAGE = "message";
        private const TIMESTAMP = "timestamp";

        private ?string $content = null;
        private ?string $timestamp = null;

        public function getContent(): string
        {
            return $this->content;
        }

        public function setContent(string $content): void
        {
            $this->content = $content;
        }

        public function getTimestamp(): string
        {
            return $this->timestamp;
        }

        public function setTimestamp(string $timestamp): void
        {
            $this->timestamp = $timestamp;
        }

        public function jsonSerialize(): array
        {
            return [
                self::MESSAGE => $this->getContent(),
                self::TIMESTAMP => $this->getTimestamp(),
            ];
        }
    }
}