<?php

namespace App\Api\Exceptions {

    /**
     * All error code definitions.
     * @package App\Exception
     */
    class ErrorCodes
    {
        /**
         * Is used if there is a undefined error.
         */
        public const UNDEFINED = -1;

        /**
         * Is used if a parameter is not valid.
         */
        public const PARAMETER_INVALID = 1;

        /**
         * Is used if the bearer was not found.
         */
        public const BEARER_NOT_FOUND = 2;

        /**
         * Is used if the request causes a HTTP 400 while sending a message.
         */
        public const INVALID_MESSAGE = 3;

        /**
         * Is used if a signature is not valid
         */
        public const INVALID_SIGNATURE = 4;
    }
}