<?php declare(strict_types=1);

namespace App\Api\Exceptions {

    use Exception;
    use JetBrains\PhpStorm\Pure;

    /**
     * Will be thrown if there is an error during the signature creation process.
     * @package App\Exception
     */
    class AuthorizationException extends BusinessException
    {
        /**
         * Constructor.
         * @param string $message The message.
         * @param int $code The code.
         * @param Exception|null $previous Previous exception.
         */
        #[Pure] public function __construct(string $message, int $code, Exception $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }
}