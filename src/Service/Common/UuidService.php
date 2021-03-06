<?php declare(strict_types=1);

namespace App\Service\Common {

    use JetBrains\PhpStorm\Pure;

    /**
     * Service to generate UUIDs.
     * @package App\Service\Common
     */
    class UuidService
    {

        /**
         * Generate a new UUID.
         * @return string .
         */
        #[Pure] public static function newUuid(): string
        {
            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }

    }
}