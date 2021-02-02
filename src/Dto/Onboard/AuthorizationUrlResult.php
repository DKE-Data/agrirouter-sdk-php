<?php declare(strict_types=1);

namespace App\Dto\Onboard {

    /**
     * Data transfer object for the communication.
     * @package App\Dto\Onboard
     */
    class AuthorizationUrlResult
    {
        private ?string $authorizationUrl;
        private ?string $state;

        public function __construct(?string $authorizationUrl, ?string $state)
        {
            $this->authorizationUrl = $authorizationUrl;
            $this->state = $state;
        }

        public function getAuthorizationUrl(): ?string
        {
            return $this->authorizationUrl;
        }

        public function setAuthorizationUrl(?string $authorizationUrl): void
        {
            $this->authorizationUrl = $authorizationUrl;
        }

        public function getState(): ?string
        {
            return $this->state;
        }

        public function setState(?string $state): void
        {
            $this->state = $state;
        }
    }
}