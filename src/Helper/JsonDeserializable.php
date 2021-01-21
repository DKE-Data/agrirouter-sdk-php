<?php declare(strict_types=1);

namespace App\Helper;

/**
 * Interface JsonDeserializable - Classes that implement this Interface deserialize itself out of json data
 * @package App\Helper
 */
interface JsonDeserializable
{
    /**
     * Creates a new object of itself with the data of a given array
     * @param array $data Array with object data.
     * @return self New onboarding response created from data array
     */
    public function jsonDeserialize(array $data): self;
}