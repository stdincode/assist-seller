<?php

namespace App\DataModels\Entities;

class TelegramClientMessage extends AbstractTelegramClientMessage
{
    private int $id;
    private string $telegramMessageId;
    private string $telegramMessage;
    private \DateTime $createdAt;

    public function __construct(
        int $id,
        string $telegramMessageId,
        string $telegramMessage,
        \DateTime $createdAt
    )
    {
        $this->id = $id;
        $this->telegramMessageId = $telegramMessageId;
        $this->telegramMessage = $telegramMessage;
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTelegramMessageId(): string
    {
        return $this->telegramMessageId;
    }

    /**
     * @return string
     */
    public function getTelegramMessage(): string
    {
        return $this->telegramMessage;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

}
