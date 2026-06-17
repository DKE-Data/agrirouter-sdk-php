<?php declare(strict_types=1);

namespace App\Api\Dto {

    use JsonException;

    /**
     * Classes that implement this Interface deserialize itself out of json data.
     * @package App\Helper
     */
    interface JsonDeserializableInterface
    {
        /**
         * Creates a new object of itself with the data of a given array or string.
         * @param array|string $jsonData String with json data.
         * @return self New onboard response created from data array
         * @throws JsonException Could be thrown if th entity can not be deserialized.
         */
        public function jsonDeserialize($jsonData);
    }
}