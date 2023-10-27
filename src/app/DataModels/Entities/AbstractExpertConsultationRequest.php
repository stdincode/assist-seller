<?php

namespace App\DataModels\Entities;

abstract class AbstractExpertConsultationRequest implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return AbstractExpert
     */
    abstract public function getExpert(): AbstractExpert;

    /**
     * @return AbstractExpertConsultationRequestStatus
     */
    abstract public function getStatus(): AbstractExpertConsultationRequestStatus;

    /**
     * @return AbstractConsultationRequest|null
     */
    abstract public function getConsultationRequest(): ?AbstractConsultationRequest;

    /**
     * @return \DateTime
     */
    abstract public function getCreatedAt(): \DateTime;

    /**
     * @return \DateTime
     */
    abstract public function getUpdatedAt(): \DateTime;

    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'expert' => $this->getExpert()->asArray(),
            'status' => $this->getStatus()->asArray(),
            'consultation_request' => $this->getConsultationRequest()?->asArray(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }

}
