<?php

namespace App\Dto\Messaging\Inner;

/**
 * Data transfer object for the communication.
 * @package App\Dto\Messaging\Inner
 */
class Message
{
    private string $content;
    private string $timestamp;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = $timestamp;
    }


}