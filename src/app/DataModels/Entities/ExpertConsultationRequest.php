<?php

namespace App\DataModels\Entities;

class ExpertConsultationRequest extends AbstractExpertConsultationRequest
{
    private int $id;
    private ?AbstractConsultationRequest $consultationRequest;
    private AbstractExpertConsultationRequestStatus $status;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;
    private AbstractExpert $expert;

    public function __construct(
        int $id,
        AbstractExpert $expert,
        AbstractExpertConsultationRequestStatus $status,
        \DateTime $createdAt,
        \DateTime $updatedAt = null,
        AbstractConsultationRequest $consultationRequest = null
    )
    {
        $this->id = $id;
        $this->expert = $expert;
        $this->consultationRequest = $consultationRequest;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getExpert(): AbstractExpert
    {
        return $this->expert;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): AbstractExpertConsultationRequestStatus
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getConsultationRequest(): ?AbstractConsultationRequest
    {
        return $this->consultationRequest;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}
