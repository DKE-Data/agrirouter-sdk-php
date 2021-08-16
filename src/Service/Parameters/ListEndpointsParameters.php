<?php

namespace App\Service\Parameters {

    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to list endpoints.
     * @package App\Service\Parameters
     */
    class ListEndpointsParameters extends MessageParameters
    {

        private ?string $technicalMessageType = null;
        private ?string $direction = null;
        private bool $filtered = false;

        public function getTechnicalMessageType(): string
        {
            return $this->technicalMessageType;
        }

        public function setTechnicalMessageType(string $technicalMessageType): void
        {
            $this->technicalMessageType = $technicalMessageType;
        }

        public function getDirection(): string
        {
            return $this->direction;
        }

        public function setDirection(string $direction): void
        {
            $this->direction = $direction;
        }

        public function isFiltered(): bool
        {
            return $this->filtered;
        }

        public function setFiltered(bool $filtered): void
        {
            $this->filtered = $filtered;
        }

    }
}