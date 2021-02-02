<?php

namespace App\Api\Builder {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Capability;
    use App\Definitions\CapabilityTypeDefinitions;

    /**
     * Builder for capabilities.
     * @package App\Api\Builder
     */
    class CapabilityBuilder
    {
        private array $capabilities = [];

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withTaskdata(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::ISO_11783_TASKDATA_ZIP);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withDeviceDescription(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::ISO_11783_DEVICE_DESCRIPTION_PROTOBUF);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withTimeLog(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::ISO_11783_TIMELOG_PROTOBUF);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withBmp(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::IMG_BMP);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withJpg(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::IMG_JPEG);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withPng(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::IMG_PNG);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withShape(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::SHP_SHAPE_ZIP);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withPdf(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::DOC_PDF);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withAvi(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::VID_AVI);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withMp4(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::VID_MP4);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withWmv(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::VID_WMV);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Add capability.
         * @param int $direction Direction, could be sending or retrieving.
         * @return $this Instance of the builder for fluent style.
         */
        public function withGpsInfo(int $direction): CapabilityBuilder
        {
            $capability = new Capability();
            $capability->setDirection($direction);
            $capability->setTechnicalMessageType(CapabilityTypeDefinitions::GPS_INFO);
            array_push($this->capabilities,$capability);
            return $this;
        }

        /**
         * Build.
         * @return array Array containing all the capabilities defined.
         */
        public function build(): array
        {
            return $this->capabilities;
        }

    }
}