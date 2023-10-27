<?php

namespace App\DataModels\Entities;

class Consultation extends AbstractConsultation
{
    private int $id;
    private ?AbstractConsultationRequest $consultationRequest;
    private AbstractConsultationStatus $status;
    private ?string $expertLink;
    private ?string $studentLink;
    private float $cost;
    private \DateTime $lastChangeStatusDatetime;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;
    private ?AbstractExpertConsultationRequest $expertConsultationRequest;
    private ?AbstractTelegramMenuSession $telegramMenuSession;
    private ?float $studentConsultationRating;
    private ?float $studentCallQualityRating;
    private ?string $studentComment;
    private ?\DateTime $studentCommentDatetime;
    private ?float $expertCallQualityRating;
    private ?string $expertComment;
    private ?\DateTime $expertCommentDatetime;

    public function __construct(
        int $id,
        AbstractConsultationStatus $status,
        float $cost,
        \DateTime $lastChangeStatusDatetime,
        \DateTime $createdAt,
        \DateTime $updatedAt = null,
        ?string $expertLink = null,
        ?string $studentLink = null,
        ?AbstractConsultationRequest $consultationRequest = null,
        ?AbstractExpertConsultationRequest $expertConsultationRequest = null,
        ?AbstractTelegramMenuSession $telegramMenuSession = null,
        ?float $studentConsultationRating = null,
        ?float $studentCallQualityRating = null,
        ?string $studentComment = null,
        ?\DateTime $studentCommentDatetime = null,
        ?float $expertCallQualityRating = null,
        ?string $expertComment = null,
        ?\DateTime $expertCommentDatetime = null
    )
    {
        $this->id = $id;
        $this->consultationRequest = $consultationRequest;
        $this->status = $status;
        $this->expertLink = $expertLink;
        $this->studentLink = $studentLink;
        $this->cost = $cost;
        $this->lastChangeStatusDatetime = $lastChangeStatusDatetime;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->expertConsultationRequest = $expertConsultationRequest;
        $this->telegramMenuSession = $telegramMenuSession;
        $this->studentConsultationRating = $studentConsultationRating;
        $this->studentCallQualityRating = $studentCallQualityRating;
        $this->studentComment = $studentComment;
        $this->studentCommentDatetime = $studentCommentDatetime;
        $this->expertCallQualityRating = $expertCallQualityRating;
        $this->expertComment = $expertComment;
        $this->expertCommentDatetime = $expertCommentDatetime;
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
    public function getExpertConsultationRequest(): ?AbstractExpertConsultationRequest
    {
        return $this->expertConsultationRequest;
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
    public function getStatus(): AbstractConsultationStatus
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getLastChangeStatusDatetime(): \DateTime
    {
        return $this->lastChangeStatusDatetime;
    }

    /**
     * @inheritDoc
     */
    public function getTelegramMenuSession(): ?AbstractTelegramMenuSession
    {
        return $this->telegramMenuSession;
    }

    /**
     * @inheritDoc
     */
    public function getExpertLink(): ?string
    {
        return $this->expertLink;
    }

    /**
     * @inheritDoc
     */
    public function getStudentLink(): ?string
    {
        return $this->studentLink;
    }

    /**
     * @inheritDoc
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @inheritDoc
     */
    public function getStudentConsultationRating(): ?float
    {
        return $this->studentConsultationRating;
    }

    /**
     * @inheritDoc
     */
    public function getStudentCallQualityRating(): ?float
    {
        return $this->studentCallQualityRating;
    }

    /**
     * @inheritDoc
     */
    public function getStudentComment(): ?string
    {
        return $this->studentComment;
    }

    /**
     * @inheritDoc
     */
    public function getStudentCommentDateTime(): ?\DateTime
    {
        return $this->studentCommentDatetime;
    }

    /**
     * @inheritDoc
     */
    public function getExpertCallQualityRating(): ?float
    {
        return $this->expertCallQualityRating;
    }

    /**
     * @inheritDoc
     */
    public function getExpertComment(): ?string
    {
        return $this->expertComment;
    }

    /**
     * @inheritDoc
     */
    public function getExpertCommentDateTime(): ?\DateTime
    {
        return $this->expertCommentDatetime;
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
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
}
