<?php declare(strict_types=1);

namespace App\Api\Exceptions {

    use JetBrains\PhpStorm\Pure;

    /**
     * Will be thrown if there is an error during the signature creation process.
     * @package App\Exception
     */
    class SignatureException extends BusinessException
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