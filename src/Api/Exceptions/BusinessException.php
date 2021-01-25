<?php declare(strict_types=1);

namespace App\Exception {

    use Exception;
    use JetBrains\PhpStorm\Pure;

    /**
     * Wrapper for all internal exceptions.
     * @package App\Exception
     */
    class BusinessException extends Exception
    {
        /**
         * Constructor.
         * @param string $message Message for the exception.
         * @param string $code Internal code.
         * @param Exception|null $previous Previous exception.
         */
        #[Pure] public function __construct(string $message, string $code, Exception $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }

        /**
         * Overriding the default method.
         * @return string -
         */
        public function __toString(): string
        {
            return __CLASS__ . ": [{$this->code}]: {$this->message}";
        }
    }
}