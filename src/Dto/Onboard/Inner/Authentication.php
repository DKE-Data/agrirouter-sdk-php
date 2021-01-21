<?php declare(strict_types=1);

namespace App\Dto\Onboard\Inner {


    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Class Authentication
     * @package App\Dto\Onboard\Inner
     */
    class Authentication implements JsonSerializable
    {
        public string $type;
        public string $secret;
        public string $certificate;

        /**
         * @return string
         */
        public function getType(): string
        {
            return $this->type;
        }

        /**
         * @param string $type
         */
        public function setType(string $type): void
        {
            $this->type = $type;
        }

        /**
         * @return string
         */
        public function getSecret(): string
        {
            return $this->secret;
        }

        /**
         * @param string $secret
         */
        public function setSecret(string $secret): void
        {
            $this->secret = $secret;
        }

        /**
         * @return string
         */
        public function getCertificate(): string
        {
            return $this->certificate;
        }

        /**
         * @param string $certificate
         */

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

        public static function createFromArray(array $data): self
        {
            $authentication = new self();
            foreach ($data as $key => $value) {

                $setterToCall = "set" . ucfirst($key);
                $authentication->$setterToCall($value);

            }
            return $authentication;
        }


    }
}