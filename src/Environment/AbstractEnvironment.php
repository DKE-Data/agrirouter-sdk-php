<?php declare(strict_types=1);

namespace App\Environment {

    /**
     * Abstract environment holding environment data.
     * @package App\Environment
     */
    abstract class AbstractEnvironment
    {

        /**
         * Returning the API prefix for several AR URLs, like the onboard URL for example.
         * @return string .
         */
        public abstract function apiPrefix(): string;

        /**
         * URL for the registration service.
         * @return string .
         */
        public abstract function registrationServiceUrl(): string;

        /**
         * URL for the authorization service.
         * @return string .
         */
        public abstract function authorizationServiceUrl(): string;

        /**
         * URL for the onboard request.
         * @return string .
         */
        public function securedOnboardUrl(): string
        {
            return $this->registrationServiceUrl() . $this->apiPrefix() . "/registration/onboard/request";
        }

        /**
         * URL for the revoking request.
         * @return string .
         */
        public function revokeUrl(): string
        {
            return $this->registrationServiceUrl() . $this->apiPrefix() . "/registration/onboard/revoke";
        }

        /**
         * URL for the onboard request.
         * @return string .
         */
        public function onboardUrl(): string
        {
            return $this->registrationServiceUrl() . $this->apiPrefix() . "/registration/onboard";
        }

        /**
         * URL for the onboard request.
         * @return string .
         */
        public function verificationUrl(): string
        {
            return $this->registrationServiceUrl() . $this->apiPrefix() . "/registration/onboard/verify";
        }

        /**
         * URL for the authorization process.
         * @param $applicationId .
         * @return string .
         */
        public function authorizationUrl(string $applicationId): string
        {
            return $this->authorizationServiceUrl() . "/application/" . $applicationId . "/authorize";
        }

    }
}