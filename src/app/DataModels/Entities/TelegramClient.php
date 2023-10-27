<?php

namespace App\DataModels\Entities;

class TelegramClient extends AbstractTelegramClient
{
    private int $id;
    private int $telegramId;
    private ?string $telegramFirstName;
    private ?string $telegramLastName;
    private ?string $telegramUsername;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;

    public function __construct(
        int $id,
        int $telegramId,
        ?string $telegramFirstName,
        ?string $telegramLastName,
        ?string $telegramUsername,
        \DateTime $createdAt,
        ?\DateTime $updatedAt = null
    )
    {
        $this->id = $id;
        $this->telegramId = $telegramId;
        $this->telegramFirstName = $telegramFirstName;
        $this->telegramLastName = $telegramLastName;
        $this->telegramUsername = $telegramUsername;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTelegramId(): int
    {
        return $this->telegramId;
    }

    public function getTelegramFirstName(): ?string
    {
        return $this->telegramFirstName;
    }

    public function getTelegramLastName(): ?string
    {
        return $this->telegramLastName;
    }

    public function getTelegramUsername(): ?string
    {
        return $this->telegramUsername;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
}
