<?php declare(strict_types=1);

namespace App\Definitions {

    /**
     * Definitions for the gateways types.
     * @package App\Definitions
     */
    class GatewayTypeDefinitions
    {

        /**
         * Type "HTTP".
         * @return string -
         */
        public static function http(): string
        {
            return "3";
        }

        /**
         * Type "MQTT".
         * @return string -
         */
        public static function mqtt(): string
        {
            return "2";
        }

    }
}