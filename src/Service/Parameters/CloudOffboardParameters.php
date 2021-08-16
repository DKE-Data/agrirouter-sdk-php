<?php

namespace App\Service\Parameters {

    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to send cloud offboard messages to the AR.
     * @package App\Service\Parameters
     */
    class CloudOffboardParameters extends MessageParameters
    {
        /**
         * @var string[]
         */
        private array $endpoints = [];

        /**
         * @return string[]
         */
        public function getEndpoints(): array
        {
            return $this->endpoints;
        }

        /**
         * @param string[] $endpoints
         */
        public function setEndpoints(array $endpoints): void
        {
            $this->endpoints = $endpoints;
        }

    }
}