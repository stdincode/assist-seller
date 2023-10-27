<?php

namespace App\DataModels\Entities;

abstract class AbstractExpertPayment implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return float
     */
    abstract public function getAmount(): float;

    /**
     * @return AbstractExpertPaymentStatus
     */
    abstract public function getStatus(): AbstractExpertPaymentStatus;

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
            'amount' => $this->getAmount(),
            'status' => $this->getStatus()->asArray(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
