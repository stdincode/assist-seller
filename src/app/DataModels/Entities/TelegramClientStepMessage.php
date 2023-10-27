<?php

namespace App\DataModels\Entities;

use Ramsey\Uuid\UuidInterface;

class TelegramClientStepMessage extends AbstractTelegramClientStepMessage
{
    private int $id;
    private UuidInterface $stepId;
    private AbstractTelegramClientMessage $telegramClientMessage;
    private \DateTime $createdAt;

    public function __construct(
        int                           $id,
        UuidInterface                 $stepId,
        AbstractTelegramClientMessage $telegramClientMessage,
        \DateTime                     $createdAt
    )
    {
        $this->id = $id;
        $this->stepId = $stepId;
        $this->telegramClientMessage = $telegramClientMessage;
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
     * @return UuidInterface
     */
    public function getStepId(): UuidInterface
    {
        return $this->stepId;
    }

    /**
     * @return AbstractTelegramClientMessage
     */
    public function getTelegramClientMessage(): AbstractTelegramClientMessage
    {
        return $this->telegramClientMessage;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

}
