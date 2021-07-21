<?php

namespace App\Api\Exceptions {

    use JetBrains\PhpStorm\Pure;

    /**
     * Will be thrown if there is any problem during the interaction with the outbox.
     * @package App\Api\Exceptions
     */
    class OutboxException extends BusinessException
    {

        /**
         * Constructor.
         * @param string $message The message.
         * @param int $code The code.
         */
        public function __construct(string $message, int $code)
        {
            parent::__construct($message, $code);
        }
    }
}