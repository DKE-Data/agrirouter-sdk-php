<?php


namespace App\Api\Service\Parameters {

    /**
     * Interface to mark parameters as validatable.
     * @package App\Api\Service\Parameters
     */
    interface Validatable
    {

        /**
         * Will validate the parameters.
         * In case of an error the method should throw an exception.
         */
        public function validate():void;

    }
}