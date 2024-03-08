<?php declare(strict_types=1);


namespace App\Environment {
    class ProductionEnvironment extends AbstractEnvironment
    {
        private const API_PREFIX = "/api/v1.0";
        private const REGISTRATION_SERVICE_URL = "https://onboard.my-agrirouter.com";
        private const AUTHORIZATION_SERVICE_URL = "https://goto.my-agrirouter.com";


        public function apiPrefix(): string
        {
            return self::API_PREFIX;
        }

        public function registrationServiceUrl(): string
        {
            return self::REGISTRATION_SERVICE_URL;
        }

        public function authorizationServiceUrl(): string
        {
            return self::AUTHORIZATION_SERVICE_URL;
        }
    }
}