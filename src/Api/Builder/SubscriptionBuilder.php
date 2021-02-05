<?php

namespace App\Api\Builder {

    use Agrirouter\Request\Payload\Endpoint\Subscription\MessageTypeSubscriptionItem;
    use App\Definitions\CapabilityTypeDefinitions;

    /**
     * Builder for subscription items.
     * @package App\Api\Builder
     */
    class SubscriptionBuilder
    {
        private array $subscriptionItems = [];

        /**
         * Add subscription.
         * @return $this Instance of the builder for fluent style.
         */
        public function withTaskdata(): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::ISO_11783_TASKDATA_ZIP);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @param array|null $ddis .
         * @param bool|null $position .
         * @return $this Instance of the builder for fluent style.
         */
        public function withDeviceDescription(?array $ddis = [], ?bool $position = null): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::ISO_11783_DEVICE_DESCRIPTION_PROTOBUF);
            $subscriptionItem->setDdis($ddis);
            $subscriptionItem->setPosition($position);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @param array|null $ddis .
         * @param bool|null $position .
         * @return $this Instance of the builder for fluent style.
         */
        public function withTimeLog(?array $ddis = [], ?bool $position = null): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::ISO_11783_TIMELOG_PROTOBUF);
            $subscriptionItem->setDdis($ddis);
            $subscriptionItem->setPosition($position);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @return $this Instance of the builder for fluent style.
         */
        public function withBmp(): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::IMG_BMP);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @return $this Instance of the builder for fluent style.
         */
        public function withJpg(): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::IMG_JPEG);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @return $this Instance of the builder for fluent style.
         */
        public function withPng(): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::IMG_PNG);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @return $this Instance of the builder for fluent style.
         */
        public function withShape(): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::SHP_SHAPE_ZIP);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @return $this Instance of the builder for fluent style.
         */
        public function withPdf(): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::DOC_PDF);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @return $this Instance of the builder for fluent style.
         */
        public function withAvi(): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::VID_AVI);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @return $this Instance of the builder for fluent style.
         */
        public function withMp4(): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::VID_MP4);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @return $this Instance of the builder for fluent style.
         */
        public function withWmv(): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::VID_WMV);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Add subscription.
         * @return $this Instance of the builder for fluent style.
         */
        public function withGpsInfo(): SubscriptionBuilder
        {
            $subscriptionItem = new MessageTypeSubscriptionItem();
            $subscriptionItem->setTechnicalMessageType(CapabilityTypeDefinitions::GPS_INFO);
            array_push($this->subscriptionItems, $subscriptionItem);
            return $this;
        }

        /**
         * Build.
         * @return array Array containing all the capabilities defined.
         */
        public function build(): array
        {
            return $this->subscriptionItems;
        }

    }
}