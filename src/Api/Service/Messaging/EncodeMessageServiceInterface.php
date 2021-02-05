<?php declare(strict_types=1);

namespace App\Api\Service\Messaging {

    use App\Dto\Messaging\EncodedMessage;

    /**
     * Interface for all services encoding messages.
     * @package App\Service\Common
     * @template T
     */
    interface EncodeMessageServiceInterface
    {
        /**
         * Encode a message using the given message parameters.
         * @param T $parameters Parameters for message sending.
         * @return EncodedMessage .
         */
        public function encode($parameters): EncodedMessage;

    }
}