<?php /** @noinspection PhpMissingParamTypeInspection */
declare(strict_types=1);

namespace App\Api\Service\Messaging {

    use App\Dto\Messaging\MessagingResult;

    /**
     * Interface for all services sending messages.
     * @package App\Service\Common
     * @template T
     */
    interface MessagingServiceInterface
    {
        /**
         * Sending a message using the given message parameters.
         * @param T $parameters Parameters for message sending.
         * @return MessagingResult .
         */
        public function send($parameters): MessagingResult;

    }
}