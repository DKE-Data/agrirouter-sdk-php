<?php declare(strict_types=1);

namespace App\Dto\Onboard {


    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Class Authentication - Data transfer object for the communication.
     * @package App\Dto\Onboard
     */
    class Authentication implements JsonSerializable
    {
        private string $type;
        private string $secret;
        private string $certificate;

        public function getType(): string
        {
            return $this->type;
        }

        public function setType(string $type): void
        {
            $this->type = $type;
        }

        public function getSecret(): string
        {
            return $this->secret;
        }

        public function setSecret(string $secret): void
        {
            $this->secret = $secret;
        }

        public function getCertificate(): string
        {
            return $this->certificate;
        }

        public function setCertificate(string $certificate): void
        {
            $this->certificate = $certificate;
        }

        #[ArrayShape(['type' => "string", 'secret' => "string", 'certificate' => "string"])]
        public function jsonSerialize(): array
        {
            return [
                'type' => $this->getType(),
                'secret' => $this->getSecret(),
                'certificate' => $this->getCertificate()
            ];
        }

        /**
         * Creates an object of type Authentication from a given data array
         * @param array $data
         * @return Authentication
         */
        public static function createFromArray(array $data): self
        {
            $onboardingResponse = new self();
            foreach ($data as $key => $value) {
                $setterToCall = "set" . ucfirst($key);
                if (is_array($value)) {
                    $classname = __NAMESPACE__ . '\\' .ucfirst($key);
                    $onboardingResponse->$setterToCall($classname::createFromArray($value));
                } else {
                    $onboardingResponse->$setterToCall($value);
                }
            }
            return $onboardingResponse;
        }
    }
}