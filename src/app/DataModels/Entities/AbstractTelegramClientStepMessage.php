<?php

namespace App\DataModels\Entities;

use Ramsey\Uuid\UuidInterface;

abstract class AbstractTelegramClientStepMessage implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return UuidInterface
     */
    abstract public function getStepId(): UuidInterface;

    /**
     * @return AbstractTelegramClientMessage
     */
    abstract public function getTelegramClientMessage(): AbstractTelegramClientMessage;

    /**
     * @return \DateTime
     */
    abstract public function getCreatedAt(): \DateTime;

    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'telegram_client_message' => $this->getTelegramClientMessage()->asArray(),
            'step_id' => $this->getStepId()->toString(),
            'created_at' => $this->getCreatedAt(),
        ];
    }

}
