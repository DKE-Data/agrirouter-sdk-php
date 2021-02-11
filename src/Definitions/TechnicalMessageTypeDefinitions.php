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
        public const CAPABILITIES = "dke:capabilities";

        /**
         * Type 'dke:subscription'.
         */
        public const SUBSCRIPTION = "dke:subscription";

        /**
         * Type 'dke:list_endpoints'.
         */
        public const LIST_ENDPOINTS = "dke:list_endpoints";

        /**
         * Type 'dke:list_endpoints'.
         */
        public const LIST_ENDPOINTS_UNFILTERED = "dke:list_endpoints_unfiltered";

        /**
         * Type 'dke:feed_confirm'.
         */
        public const FEED_CONFIRM = "dke:feed_confirm";

        /**
         * Type 'dke:feed_delete'.
         */
        public const FEED_DELETE = "dke:feed_delete";

        /**
         * Type 'dke:feed_header_query'.
         */
        public const FEED_HEADER_QUERY = "dke:feed_header_query";

        /**
         * Type 'dke:feed_message_query'.
         */
        public const FEED_MESSAGE_QUERY = "dke:feed_message_query";

        /**
         * Type 'dke:cloud_onboard_endpoints'.
         */
        public const CLOUD_ONBOARD_ENDPOINTS = "dke:cloud_onboard_endpoints";

    }
}