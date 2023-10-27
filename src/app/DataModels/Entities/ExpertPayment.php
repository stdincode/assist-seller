<?php

namespace App\DataModels\Entities;

class ExpertPayment extends AbstractExpertPayment
{
    private int $id;
    private string $amount;
    private AbstractExpertPaymentStatus $status;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;

    public function __construct(
        int                         $id,
        float                       $amount,
        AbstractExpertPaymentStatus $status,
        \DateTime                   $createdAt,
        ?\DateTime                  $updatedAt = null
    )
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->status = $status;
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
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return AbstractExpertPaymentStatus
     */
    public function getStatus(): AbstractExpertPaymentStatus
    {
        return $this->status;
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
