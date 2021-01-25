<?php declare(strict_types=1);

namespace App\Api\Service\Parameters {

    use App\Api\Exceptions\ValidationException;

    /**
     * Parameter container definition.
     * @package App\Api\Service\Parameters
     */
    class MessagingParameters extends MessageParameters implements Validatable
    {
        private array $encodedMessages = [];

        public function getEncodedMessages(): array
        {
            return $this->encodedMessages;
        }

        public function setEncodedMessages(array $encodedMessages): void
        {
            $this->encodedMessages = $encodedMessages;
        }

        public function validate(): void
        {
            parent::validate();
            if (array_count_values($this->encodedMessages)) {
                throw new ValidationException("encodedMessages");
            }
        }
    }
}