<?php

namespace Lib\Tests\Helper {

    use App\Dto\Onboard\OnboardResponse;
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

        /**
         * Create a single HTTP client with authentication.
         * @param OnboardResponse $onboardResponse -
         * @return Client -
         */
        public static function authenticatedHttpClient(OnboardResponse $onboardResponse): Client
        {
            return new Client();
        }

    }
}