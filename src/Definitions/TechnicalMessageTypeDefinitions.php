<?php


namespace App\Definitions {

    /**
     * Definitions for technical message types.
     * @package App\Definitions
     */
    class TechnicalMessageTypeDefinitions
    {
        /**
         * Empty type for several messages.
         */
        public const EMPTY = "";

        /**
         * Type 'dke:capabilities'.
         */
        public const DKE_CAPABILITIES = "dke:capabilities";

        /**
         * Type 'dke:subscription'.
         */
        public const DKE_SUBSCRIPTION = "dke:subscription";

        /**
         * Type 'dke:list_endpoints'.
         */
        public const DKE_LIST_ENDPOINTS = "dke:list_endpoints";

        /**
         * Type 'dke:list_endpoints'.
         */
        public const DKE_LIST_ENDPOINTS_UNFILTERED = "dke:list_endpoints_unfiltered";

        /**
         * Type 'dke:feed_confirm'.
         */
        public const DKE_FEED_CONFIRM = "dke:feed_confirm";

    }
}