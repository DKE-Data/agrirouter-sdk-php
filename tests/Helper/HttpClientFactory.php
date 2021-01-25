<?php

namespace Lib\Tests\Helper {

    use GuzzleHttp\Client;

    class HttpClientFactory
    {

        /**
         * Create a single HTTP client.
         * @return Client -
         */
        public static function httpClient(): Client
        {
            return new Client();
        }

    }
}