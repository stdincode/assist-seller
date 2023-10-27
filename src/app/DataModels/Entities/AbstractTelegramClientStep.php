<?php

namespace App\DataModels\Entities;

use App\DataModels\Entities\Bags\TelegramClientStepMessagesBagInterface;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractTelegramClientStep implements EntityInterface
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
     * @return int
     */
    abstract public function getTelegramMessageId(): int;

    /**
     * @return TelegramClientStepMessagesBagInterface
     */
    abstract public function getStepMessagesBag(): TelegramClientStepMessagesBagInterface;

    /**
     * @return \DateTime
     */
    abstract public function getCreatedAt(): \DateTime;

    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'step_id' => $this->getStepId()->toString(),
            'telegram_message_id' => $this->getStepId()->toString(),
            'step_messages' => $this->getStepMessagesBag()->asArray(),
            'created_at' => $this->getCreatedAt(),
        ];
    }

}
