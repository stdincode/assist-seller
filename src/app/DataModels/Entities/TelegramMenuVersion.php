<?php

namespace App\DataModels\Entities;

use Ramsey\Uuid\UuidInterface;

class TelegramMenuVersion extends AbstractTelegramMenuVersion
{
    private int $id;
    private AbstractTelegramMenu $telegramMenu;
    private UuidInterface $startStepId;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;


    public function __construct(
        int                  $id,
        AbstractTelegramMenu $telegramMenu,
        UuidInterface        $startStepId,
        \DateTime            $createdAt,
        ?\DateTime           $updatedAt
    )
    {
        $this->id = $id;
        $this->telegramMenu = $telegramMenu;
        $this->startStepId = $startStepId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return AbstractTelegramMenu
     */
    public function getTelegramMenu(): AbstractTelegramMenu
    {
        return $this->telegramMenu;
    }

    /**
     * @return UuidInterface
     */
    public function getStartStepId(): UuidInterface
    {
        return $this->startStepId;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
}
