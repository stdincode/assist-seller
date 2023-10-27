<?php

namespace App\DataModels\Entities;

use Ramsey\Uuid\UuidInterface;

abstract class AbstractTelegramMenuVersion implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return AbstractTelegramMenu
     */
    abstract public function getTelegramMenu(): AbstractTelegramMenu;

    /**
     * @return UuidInterface
     */
    abstract public function getStartStepId(): UuidInterface;

    /**
     * @return \DateTime
     */
    abstract public function getCreatedAt(): \DateTime;

    /**
     * @return ?\DateTime
     */
    abstract public function getUpdatedAt(): ?\DateTime;

    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'telegram_menu' => $this->getTelegramMenu()->asArray(),
            'start_step' => $this->getStartStepId()->toString(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
