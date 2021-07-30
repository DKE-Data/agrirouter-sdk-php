<?php

namespace App\Dto\Messaging\Http {

    use App\Api\Dto\JsonDeserializableInterface;
    use App\Dto\Messaging\Http\Inner\OutboxMessage;

    /**
     * Response from the outbox.
     * @package App\Dto\Messaging\Http
     */
    class OutboxResponse implements JsonDeserializableInterface
    {
        private ?string $statusCode = null;
        private ?array $messages = null;

        public function jsonDeserialize(array|string $jsonData): JsonDeserializableInterface
        {
            if (is_string($jsonData)) {
                $decodedJsonDataArray = json_decode($jsonData, true);
            } else {
                $decodedJsonDataArray = $jsonData;
            }
            foreach ($decodedJsonDataArray as $message) {
                $this->messages = [];
                $outboxMessage = new OutboxMessage();
                $outboxMessage->jsonDeserialize($message);
                array_push($this->messages, $outboxMessage);
            }
            return $this;
        }

        public function getStatusCode(): string
        {
            return $this->statusCode;
        }

        public function setStatusCode(string $statusCode): void
        {
            $this->statusCode = $statusCode;
        }

        /**
         * @return OutboxMessage[]
         */
        public function getMessages(): array
        {
            return $this->messages;
        }

        public function setMessages(array $messages): void
        {
            $this->messages = $messages;
        }

    }
}