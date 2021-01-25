<?php

namespace App\Api\Exceptions {

    use App\Exception\BusinessException;
    use App\Exception\ErrorCodes;
    use JetBrains\PhpStorm\Pure;

    /**
     * Will be thrown if the parameters are not valid.
     * @package App\Api\Exceptions
     */
    class ValidationException extends BusinessException
    {

        /**
         * ValidationException constructor.
         * @param string $invalidParameter Invalid parameter
         */
        #[Pure] public function __construct(string $invalidParameter)
        {
            parent::__construct("Parameters passed are not valid. The following parameter is invalid >>> $invalidParameter", ErrorCodes::PARAMETER_INVALID);
        }
    }
}