<?php

namespace App\Service\Parameters {

    use Agrirouter\Request\Payload\Endpoint\Subscription;
    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to send subscriptions to the AR.
     * @package App\Service\Parameters
     */
    class SubscriptionParameters extends MessageParameters
    {
        /**
         * @var Subscription\MessageTypeSubscriptionItem[]
         */
        private ?array $subscriptionItems = null;

        /**
         * @return Subscription\MessageTypeSubscriptionItem[]
         */
        public function getSubscriptionItems(): array
        {
            return $this->subscriptionItems;
        }

        /**
         * @param Subscription\MessageTypeSubscriptionItem[] $subscriptionItems
         */
        public function setSubscriptionItems(array $subscriptionItems): void
        {
            $this->subscriptionItems = $subscriptionItems;
        }
    }
}
