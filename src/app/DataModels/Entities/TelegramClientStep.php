<?php

namespace App\DataModels\Entities;

use App\DataModels\Entities\Bags\TelegramClientStepMessagesBagInterface;
use Ramsey\Uuid\UuidInterface;

class TelegramClientStep extends AbstractTelegramClientStep
{
    private int $id;
    private UuidInterface $stepId;
    private int $telegramMessageId;
    private TelegramClientStepMessagesBagInterface $stepMessagesBag;
    private \DateTime $createdAt;

    public function __construct(
        int                                    $id,
        UuidInterface                          $stepId,
        int                                    $telegramMessageId,
        TelegramClientStepMessagesBagInterface $stepMessagesBag,
        \DateTime                              $createdAt
    )
    {
        $this->id = $id;
        $this->stepId = $stepId;
        $this->telegramMessageId = $telegramMessageId;
        $this->stepMessagesBag = $stepMessagesBag;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStepId(): UuidInterface
    {
        return $this->stepId;
    }

    public function getTelegramMessageId(): int
    {
        return $this->telegramMessageId;
    }

    public function getStepMessagesBag(): TelegramClientStepMessagesBagInterface
    {
        return $this->stepMessagesBag;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
