<?php

namespace App\DataModels\Entities;

abstract class AbstractTelegramClientMessage implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return string
     */
    abstract public function getTelegramMessageId(): string;

    /**
     * @return string
     */
    abstract public function getTelegramMessage(): string;

    /**
     * @return \DateTime
     */
    abstract public function getCreatedAt(): \DateTime;

    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'telegram_message_id' => $this->getTelegramMessageId(),
            'telegram_message' => $this->getTelegramMessage(),
            'created_at' => $this->getCreatedAt(),
        ];
    }

}
