<?php

namespace App\DataModels\Entities;

abstract class AbstractConsultation implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return AbstractExpertConsultationRequest|null
     */
    abstract public function getExpertConsultationRequest(): ?AbstractExpertConsultationRequest;

    /**
     * @return AbstractConsultationRequest|null
     */
    abstract public function getConsultationRequest(): ?AbstractConsultationRequest;

    /**
     * @return AbstractConsultationStatus
     */
    abstract public function getStatus(): AbstractConsultationStatus;

    /**
     * @return \DateTime
     */
    abstract public function getLastChangeStatusDatetime(): \DateTime;

    /**
     * @return AbstractTelegramMenuSession|null
     */
    abstract public function getTelegramMenuSession(): ?AbstractTelegramMenuSession;

    /**
     * @return string|null
     */
    abstract public function getExpertLink(): ?string;

    /**
     * @return string|null
     */
    abstract public function getStudentLink(): ?string;

    /**
     * @return float
     */
    abstract public function getCost(): float;

    /**
     * @return float|null
     */
    abstract public function getStudentConsultationRating(): ?float;

    /**
     * @return float|null
     */
    abstract public function getStudentCallQualityRating(): ?float;

    /**
     * @return string|null
     */
    abstract public function getStudentComment(): ?string;

    /**
     * @return \DateTime|null
     */
    abstract public function getStudentCommentDateTime(): ?\DateTime;

    /**
     * @return float|null
     */
    abstract public function getExpertCallQualityRating(): ?float;

    /**
     * @return string|null
     */
    abstract public function getExpertComment(): ?string;

    /**
     * @return \DateTime|null
     */
    abstract public function getExpertCommentDateTime(): ?\DateTime;

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
            'expert_consultation_request' => $this->getExpertConsultationRequest()?->asArray(),
            'consultation_request' => $this->getConsultationRequest()?->asArray(),
            'telegram_menu_session' => $this->getTelegramMenuSession()?->asArray(),
            'expert_link' => $this->getExpertLink(),
            'student_link' => $this->getStudentLink(),
            'status' => $this->getStatus()->asArray(),
            'last_change_status_datetime' => $this->getLastChangeStatusDatetime(),
            'cost' => $this->getCost(),
            'student_consultation_rating' => $this->getStudentConsultationRating(),
            'student_call_quality_rating' => $this->getStudentCallQualityRating(),
            'student_comment' => $this->getStudentComment(),
            'student_comment_datetime' => $this->getStudentCommentDateTime(),
            'expert_call_quality_rating' => $this->getExpertCallQualityRating(),
            'expert_comment' => $this->getExpertComment(),
            'expert_comment_datetime' => $this->getExpertCommentDateTime(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }

}
