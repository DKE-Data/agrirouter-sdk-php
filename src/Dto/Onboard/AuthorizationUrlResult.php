<?php


namespace App\Dto\Onboard;


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