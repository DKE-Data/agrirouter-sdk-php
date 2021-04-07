<?php

namespace App\Service\Parameters {

    use Agrirouter\Commons\ChunkComponent;
    use App\Api\Service\Parameters\Parameters;
    use App\Api\Service\Parameters\ValidatableInterface;

    /**
     * Parameter class for message encoding - in this case the message header.
     * @package App\Service\Parameters
     */
    class MessageHeaderParameters extends Parameters implements ValidatableInterface
    {
        private ?string $technicalMessageType = null;
        private ?int $mode = null;
        private ?string $teamSetContextId = null;
        private ?array $recipients = null;
        private ?ChunkComponent $chunkComponent = null;

        public function validate(): void
        {
            // TODO: Implement validate() method.
        }

        public function getTechnicalMessageType(): string
        {
            return $this->technicalMessageType;
        }

        public function setTechnicalMessageType(string $technicalMessageType): void
        {
            $this->technicalMessageType = $technicalMessageType;
        }

        public function getMode(): int
        {
            return $this->mode;
        }

        public function setMode(int $mode): void
        {
            $this->mode = $mode;
        }

        public function getTeamSetContextId(): string
        {
            return $this->teamSetContextId;
        }

        public function setTeamSetContextId(string $teamSetContextId): void
        {
            $this->teamSetContextId = $teamSetContextId;
        }

        public function getRecipients(): array
        {
            return $this->recipients;
        }

        public function setRecipients(array $recipients): void
        {
            $this->recipients = $recipients;
        }

        public function getChunkComponent(): ChunkComponent
        {
            return $this->chunkComponent;
        }

        public function setChunkComponent(ChunkComponent $chunkComponent): void
        {
            $this->chunkComponent = $chunkComponent;
        }


    }
}