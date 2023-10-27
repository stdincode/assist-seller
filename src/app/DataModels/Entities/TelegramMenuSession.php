<?php

namespace App\DataModels\Entities;

use App\DataModels\Entities\Bags\TelegramClientStepsBagInterface;

class TelegramMenuSession extends AbstractTelegramMenuSession
{
    private int $id;
    private int $telegramChatId;
    private AbstractTelegramClient $telegramClient;
    private AbstractTelegramMenuVersion $telegramMenuVersion;
    private TelegramClientStepsBagInterface $telegramClientStepsBag;
    private ?\DateTime $closedAt;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;

    public function __construct(
        int                               $id,
        int                               $telegramChatId,
        AbstractTelegramClient            $telegramClient,
        AbstractTelegramMenuVersion       $telegramMenuVersion,
        TelegramClientStepsBagInterface   $telegramClientStepsBag,
        \DateTime                         $createdAt,
        ?\DateTime                        $updatedAt = null,
        ?\DateTime                        $closedAt = null
    )
    {
        $this->id = $id;
        $this->telegramChatId = $telegramChatId;
        $this->telegramClient = $telegramClient;
        $this->telegramMenuVersion = $telegramMenuVersion;
        $this->telegramClientStepsBag = $telegramClientStepsBag;
        $this->closedAt = $closedAt;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTelegramChatId(): int
    {
        return $this->telegramChatId;
    }

    public function getTelegramClient(): AbstractTelegramClient
    {
        return $this->telegramClient;
    }

    public function getTelegramMenuVersion(): AbstractTelegramMenuVersion
    {
        return $this->telegramMenuVersion;
    }

    public function getClientStepsBag(): TelegramClientStepsBagInterface
    {
        return $this->telegramClientStepsBag;
    }

    public function getClosedAt(): ?\DateTime
    {
        return $this->closedAt;
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
