<?php

namespace App\DataModels\Entities;

abstract class AbstractConsultationRequest implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return string
     */
    abstract public function getText(): string;

    /**
     * @return AbstractTelegramMenuSession|null
     */
    abstract public function getTelegramMenuSession(): ?AbstractTelegramMenuSession;

    /**
     * @return AbstractPlace
     */
    abstract public function getPlace(): AbstractPlace;

    /**
     * @return AbstractSpecialization
     */
    abstract public function getSpecialization(): AbstractSpecialization;

    /**
     * @return AbstractStudent
     */
    abstract public function getStudent(): AbstractStudent;

    /**
     * @return \DateTime
     */
    abstract public function getConsultationDateTime(): \DateTime;

    /**
     * @return AbstractConsultationRequestStatus
     */
    abstract public function getStatus(): AbstractConsultationRequestStatus;

    /**
     * @return \DateTime
     */
    abstract public function getLastChangeStatusDatetime(): \DateTime;

    /**
     * @return \DateTime
     */
    abstract public function getCreatedAt(): \DateTime;



    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'text' => $this->getText(),
            'consultation_datetime' => $this->getConsultationDateTime(),
            'telegram_menu_session' => $this->getTelegramMenuSession()?->asArray(),
            'place' => $this->getPlace()->asArray(),
            'specialization' => $this->getSpecialization()->asArray(),
            'student' => $this->getStudent()->asArray(),
            'status' => $this->getStatus()->asArray(),
            'last_change_status_datetime' => $this->getLastChangeStatusDatetime(),
            'created_at' => $this->getCreatedAt(),
        ];
    }

}
