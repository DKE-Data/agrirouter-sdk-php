<?php

namespace App\Api\Exceptions {

    use JetBrains\PhpStorm\Pure;

    class MessagingException extends BusinessException
    {

        /**
         * Constructor.
         * @param string $message The message.
         * @param int $code The code.
         */
        #[Pure] public function __construct(string $message, int $code)
        {
            parent::__construct($message, $code);
        }
    }
}