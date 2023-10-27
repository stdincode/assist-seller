<?php

namespace App\DataModels\Entities;

abstract class AbstractStudent implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return string
     */
    abstract public function getFirstName(): string;

    /**
     * @return AbstractTelegramClient|null
     */
    abstract public function getTelegramClient(): ?AbstractTelegramClient;

    /**
     * @return int|null
     */
    abstract public function getContactPhoneNumber(): ?int;

    /**
     * @return bool
     */
    abstract public function getIsBlocked(): bool;

    /**
     * @return float|null
     */
    abstract public function getExpenses(): ?float;

    /**
     * @return \DateTime
     */
    abstract public function getCreatedAt(): \DateTime;

    /**
     * @return \DateTime|null
     */
    abstract public function getUpdatedAt(): ?\DateTime;

    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'first_name' => $this->getFirstName(),
            'contact_phone_number' => $this->getContactPhoneNumber(),
            'is_blocked' => $this->getIsBlocked(),
            'telegram_client' => $this->getTelegramClient()?->asArray(),
            'expenses' => $this->getExpenses(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
