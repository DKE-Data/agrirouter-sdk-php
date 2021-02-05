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

        /**
         * Is used if an unknown field is found while deserializing json data;
         */
        public const UNKNOWN_FIELD_IN_JSON_DATA = 9;

        /**
         * Is used if the token from the authorization response can not be decoded.
         */
        public const COULD_NOT_PARSE_AUTHORIZATION_TOKEN = 10;

        /**
         * Is used if the endpoint can not fetch messages from the outbox.
         */
        public const COULD_NOT_FETCH_MESSAGES_FROM_OUTBOX = 11;

        /**
         * Is used if the message from the AR could not be decoded.
         */
        public const COULD_NOT_DECODE_MESSAGE = 12;

        /**
         * Is used if the message details from a message could not be decoded.
         */
        public const COULD_NOT_DECODE_DETAILS = 13;

        /**
         * Is used if the response status of the agrirouter is not expected in the actual context.
         */
        public const UNEXPECTED_RESPONSE_STATUS = 14;

        /**
         * Is used if the authorization of the revoke request failed.
         */
        public const AUTHORIZATION_FAILED = 15;
    }
}