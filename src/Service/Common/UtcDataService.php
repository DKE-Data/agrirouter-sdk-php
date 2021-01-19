<?php declare(strict_types=1);

namespace App\Service\Common {

    use DateTime;

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
         * @return string Formatted timestamp in UTC.
         */
        public static function now(): string
        {
            $d = new DateTime();
            return $d->format("Y-m-d\Th:m:s.v\Z");
        }
    }
}