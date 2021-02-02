<?php

namespace App\Api\Exceptions {

    use JetBrains\PhpStorm\Pure;

    /**
     * Will be thrown if the parameters are not valid.
     * @package App\Api\Exceptions
     */
    class ValidationException extends BusinessException
    {

        /**
         * Constructor.
         * @param string $invalidParameter Invalid parameter
         */
        #[Pure] public function __construct(string $invalidParameter)
        {
            parent::__construct("Parameters passed are not valid. The following parameter is invalid >>> $invalidParameter", ErrorCodes::PARAMETER_INVALID);
        }

    }
}