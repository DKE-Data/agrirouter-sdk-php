<?php

namespace Lib\Tests\Service\Common {

    /**
     * Timer.
     * @package Lib\Tests\Service\Common
     */
    class SleepTimer
    {

        /**
         * Sleep for a dedicated time and let the AR process the message.
         * @param int $seconds Seconds to sleep.
         */
        public static function letTheAgrirouterProcessTheMessage(int $seconds = 3)
        {
            sleep($seconds);
        }

    }
}