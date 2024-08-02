<?php declare(strict_types=1);

namespace App\Environment {

    /**
     * Container for QA specific constants.
     * @package App\Environment
     */
    class Ar2QualityAssuranceEnvironment extends AbstractEnvironment
    {
        private const API_PREFIX = "/api/v1.0";
        private const REGISTRATION_SERVICE_URL =
            "https://endpoint-service.qa.agrirouter.farm";
        private const AUTHORIZATION_SERVICE_URL = "https://app.qa.agrirouter.farm";


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