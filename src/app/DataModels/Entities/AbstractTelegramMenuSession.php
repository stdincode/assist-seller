<?php

namespace App\DataModels\Entities;

use App\DataModels\Entities\Bags\TelegramClientStepsBagInterface;

abstract class AbstractTelegramMenuSession implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return AbstractTelegramClient
     */
    abstract public function getTelegramClient(): AbstractTelegramClient;

    /**
     * @return int
     */
    abstract public function getTelegramChatId(): int;

    /**
     * @return AbstractTelegramMenuVersion
     */
    abstract public function getTelegramMenuVersion(): AbstractTelegramMenuVersion;

    /**
     * @return TelegramClientStepsBagInterface
     */
    abstract public function getClientStepsBag(): TelegramClientStepsBagInterface;

    /**
     * @return \DateTime|null
     */
    abstract public function getClosedAt(): ?\DateTime;

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
            'telegram_chat_id' => $this->getTelegramChatId(),
            'menu_version' => $this->getTelegramMenuVersion()->asArray(),
            'client' => $this->getTelegramClient()->asArray(),
            'client_steps' => $this->getClientStepsBag()->asArray(),
            'opened_at' => $this->getClosedAt(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
