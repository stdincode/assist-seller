<?php

namespace App\DataModels\Entities;

class Student extends AbstractStudent
{
    private int $id;
    private string $firstName;
    private ?AbstractTelegramClient $telegramClient;
    private ?int $contactPhoneNumber;
    private bool $isBlocked;
    private ?float $expenses;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;

    public function __construct(
        int                    $id,
        string                 $firstName,
        string                 $isBlocked,
        \DateTime              $createdAt,
        ?AbstractTelegramClient $telegramClient = null,
        ?int                   $contactPhoneNumber = null,
        ?float                 $expenses = null,
        ?\DateTime             $updatedAt = null
    )
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->telegramClient = $telegramClient;
        $this->isBlocked = $isBlocked;
        $this->contactPhoneNumber = $contactPhoneNumber;
        $this->expenses = $expenses;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getTelegramClient(): ?AbstractTelegramClient
    {
        return $this->telegramClient;
    }

    public function getContactPhoneNumber(): int
    {
        return $this->contactPhoneNumber;
    }

    public function getIsBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function getExpenses(): ?float
    {
        return $this->expenses;
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
