<?php

namespace App\Service\Parameters {

    use App\Api\Service\Parameters\MessageParameters;

    /**
     * Parameter class to send subscriptions to the AR.
     * @package App\Service\Parameters
     */
    class SubscriptionParameters extends MessageParameters
    {
        private array $subscriptionItems;

        public function getSubscriptionItems(): array
        {
            return $this->subscriptionItems;
        }

        public function setSubscriptionItems(array $subscriptionItems): void
        {
            $this->subscriptionItems = $subscriptionItems;
        }
    }
}