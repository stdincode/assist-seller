<?php

namespace App\DataModels\Entities;

abstract class AbstractTelegramClient implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return int
     */
    abstract public function getTelegramId(): int;

    /**
     * @return string|null
     */
    abstract public function getTelegramFirstName(): ?string;

    /**
     * @return string|null
     */
    abstract public function getTelegramLastName(): ?string;

    /**
     * @return string|null
     */
    abstract public function getTelegramUsername(): ?string;

    /**
     * @return \DateTime
     */
    abstract public function getCreatedAt(): \DateTime;

    abstract public function getUpdatedAt(): ?\DateTime;

    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'telegram_id' => $this->getTelegramId(),
            'telegram_first_name' => $this->getTelegramFirstName(),
            'telegram_last_name' => $this->getTelegramLastName(),
            'telegram_username' => $this->getTelegramUsername(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
