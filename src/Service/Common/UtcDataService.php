<?php declare(strict_types=1);

namespace App\Service\Common {

    use DateTime;
    use JetBrains\PhpStorm\Pure;

    /**
     * Service to generate timestamps and hold UTC specific functions.
     * @package App\Service\Common
     */
    class UtcDataService
    {
        /**
         * The AR requires a timestamp given in the following format:
         *
         * 2018-06-20T07:29:23.457Z
         *
         * @return string -
         */
        public static function now(): string
        {
            $d = new DateTime();
            return $d->format("Y-m-d\TH:i:s.v\Z");
        }

        /**
         * Delivering the current time zone as a string representation.
         * @param int $offset -
         * @return string -
         */
        #[Pure] public static function timeZone(int $offset): string
        {
            return sprintf("%s%02d:00", ($offset >= 0) ? '+' : '-', abs($offset / 3600));
        }

        /**
         * Delivering the current date using a unix timestamp format.
         * @return string
         */
        public static function nowAsUnixTimestamp(): string
        {
            return sprintf("%s", time());
        }
    }
}