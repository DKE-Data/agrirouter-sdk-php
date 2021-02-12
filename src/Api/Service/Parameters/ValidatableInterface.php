<?php

namespace App\Api\Service\Parameters {

    use App\Api\Exceptions\ValidationException;

    /**
     * Interface to mark parameters as validatable.
     * @package App\Api\Service\Parameters
     */
    interface ValidatableInterface
    {

        /**
         * Will validate the parameters.
         * @throws ValidationException Will be thrown if there is a validation error.
         */
        public function validate(): void;

    }
}