<?php declare(strict_types=1);

namespace App\Dto\Onboard {

    /**
     * Data transfer object for the communication.
     * @package App\Dto\Onboard
     */
    class AuthorizationResult
    {
        private ?string $state = null;
        private ?string $signature = null;
        private ?string $token = null;
        private ?string $error = null;

        public function getState(): ?string
        {
            return $this->state;
        }

        public function setState(?string $state): void
        {
            $this->state = $state;
        }

        public function getSignature(): ?string
        {
            return $this->signature;
        }

        public function setSignature(?string $signature): void
        {
            $this->signature = $signature;
        }

        public function getToken(): ?string
        {
            return $this->token;
        }

        public function setToken(?string $token): void
        {
            $this->token = $token;
        }

        public function getError(): ?string
        {
            return $this->error;
        }

        public function setError(?string $error): void
        {
            $this->error = $error;
        }
    }
}