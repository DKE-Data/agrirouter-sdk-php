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
         * Is used if a signature is not valid.
         */
        public const INVALID_SIGNATURE = 4;

        /**
         * Is used if an error occurs while verifying the signature.
         */
        public const SIGNATURE_VERIFICATION_ERROR = 5;

        /**
         * Is used if an error occurs while counting the authorization result parameters.
         */
        public const AUTHORIZATION_RESULT_PARAMETER_COUNT_ERROR = 6;

        /**
         * Is used if an authorization result parameters has no value.
         */
        public const AUTHORIZATION_PARAMETER_VALUE_MISSING = 7;

        /**
         * Is used if an unknown authorization result parameter has been detected.
         */
        public const UNKNOWN_AUTHORIZATION_PARAMETER = 8;
    }
}