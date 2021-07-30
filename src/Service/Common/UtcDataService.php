<?php declare(strict_types=1);

namespace App\Service\Common {

    use DateTime;
    use DateTimeZone;
    use Google\Protobuf\Timestamp;
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
         * @return string The current time.
         */
        public static function now(): string
        {
            $d = new DateTime(null, new DateTimeZone('UTC'));
            return $d->format("Y-m-d\TH:i:s.v\Z");
        }

        /**
         * The AR requires a timestamp given in a special format:
         *
         * 2018-06-20T07:29:23.457Z
         *
         * @param DateTime $d Date to be formatted.
         * @return string The current time.
         */
        public static function getAsTimestamp(DateTime $d): string
        {
            $d->setTimezone(new DateTimeZone('UTC'));
            return $d->format("Y-m-d\TH:i:s.v\Z");
        }

        /**
         * Delivering the current time zone as a string representation.
         * @param int $offset .
         * @return string The current time zone.
         */
        #[Pure] public static function timeZone(int $offset): string
        {
            return sprintf("%s%02d:00", ($offset >= 0) ? '+' : '-', abs($offset / 3600));
        }

        /**
         * Delivering the current date using a unix timestamp format.
         * @return string The current timestamp as UNIX format.
         */
        public static function nowAsUnixTimestamp(): string
        {
            return sprintf("%s", time());
        }

        /**
         * Delivering the current date using a timestamp.
         * @return Timestamp The current timestamp.
         */
        public static function nowAsTimestamp(): Timestamp
        {
            $timestamp = new Timestamp();
            $timestamp->fromDateTime(new DateTime());
            return $timestamp;
        }
    }
}