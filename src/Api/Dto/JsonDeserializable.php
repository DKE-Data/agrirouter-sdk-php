<?php declare(strict_types=1);

namespace App\Api\Dto {

    /**
     * Interface JsonDeserializable - Classes that implement this Interface deserialize itself out of json data
     * @package App\Helper
     */
    interface JsonDeserializable
    {
        /**
         * Creates a new object of itself with the data of a given array or string
         * @param array|string $jsonData String with json data.
         * @return self New onboard response created from data array
         */
        public function jsonDeserialize(array|string $jsonData): self;
    }
}