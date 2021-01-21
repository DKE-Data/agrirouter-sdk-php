<?php declare(strict_types=1);

namespace App\Dto\Onboard {


    use App\Helper\JsonDeserializable;
    use Exception;
    use JetBrains\PhpStorm\ArrayShape;
    use JsonSerializable;

    /**
     * Class Authentication - Data transfer object for the communication.
     * @package App\Dto\Onboard
     */
    class Authentication implements JsonSerializable,JsonDeserializable
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

        public function jsonDeserialize(array $data): self
        {
            foreach ($data as $key => $value) {
                try {
                    $this->$key = $value;
                } catch (Exception $ex) {
                    echo $ex;
                }
            }
            return $this;
        }
    }
}