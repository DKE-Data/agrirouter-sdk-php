<?php declare(strict_types=1);

namespace App\Definitions {

    /**
     * Definitions for the certification types.
     * @package App\Definitions
     */
    class CertificationTypeDefinitions
    {

        /**
         * Type "PEM".
         * @return string -
         */
        public static function pem(): string
        {
            return "PEM";
        }

        /**
         * Type "P12".
         * @return string -
         */
        public static function p12(): string
        {
            return "P12";
        }

    }
}