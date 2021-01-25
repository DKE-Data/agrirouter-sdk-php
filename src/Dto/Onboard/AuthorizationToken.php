<?php declare(strict_types=1);


namespace App\Dto\Onboard;


use App\Api\Dto\JsonDeserializable;

class AuthorizationToken implements JsonDeserializable
{
    private ?string $account = null;
    private ?string $regcode = null;
    private ?string $expires = null;

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(?string $account): void
    {
        $this->account = $account;
    }

    public function getRegcode(): ?string
    {
        return $this->regcode;
    }

    public function setRegcode(?string $regcode): void
    {
        $this->regcode = $regcode;
    }

    public function getExpires(): ?string
    {
        return $this->expires;
    }

    public function setExpires(?string $expires): void
    {
        $this->expires = $expires;
    }

    public function jsonDeserialize(array $data): self
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $classname = __NAMESPACE__ . '\\' .ucfirst($key);
                $object = new $classname();
                $this->$key = $object->jsonDeserialize($value);
            } else {
                try {
                    $this->$key = $value;
                } catch (Exception $ex){
                    echo $ex;
                }
            }
        }
        return $this;
    }
}