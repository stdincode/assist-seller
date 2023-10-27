<?php

namespace App\DataModels\Entities;

class ConsultationRequest extends AbstractConsultationRequest
{
    private int $id;
    private string $text;
    private \DateTime $consultationDateTime;
    private ?AbstractTelegramMenuSession $telegramMenuSession;
    private AbstractPlace $place;
    private AbstractSpecialization $specialization;
    private AbstractStudent $student;
    private AbstractConsultationRequestStatus $status;
    private \DateTime $createdAt;
    private \DateTime $lastChangeStatusDatetime;

    public function __construct(
        int $id,
        AbstractStudent $student,
        string $text,
        \DateTime $consultationDateTime,
        AbstractPlace $place,
        AbstractSpecialization $specialization,
        AbstractConsultationRequestStatus $status,
        \DateTime $lastChangeStatusDatetime,
        \DateTime $createdAt,
        ?AbstractTelegramMenuSession $telegramMenuSession = null
    )
    {
        $this->id = $id;
        $this->text = $text;
        $this->consultationDateTime = $consultationDateTime;
        $this->telegramMenuSession = $telegramMenuSession;
        $this->place = $place;
        $this->specialization = $specialization;
        $this->student = $student;
        $this->status = $status;
        $this->lastChangeStatusDatetime = $lastChangeStatusDatetime;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getConsultationDateTime(): \DateTime
    {
        return $this->consultationDateTime;
    }


    public function getTelegramMenuSession(): ?AbstractTelegramMenuSession
    {
        return $this->telegramMenuSession;
    }

    public function getPlace(): AbstractPlace
    {
        return $this->place;
    }

    public function getSpecialization(): AbstractSpecialization
    {
        return $this->specialization;
    }

    public function getStudent(): AbstractStudent
    {
        return $this->student;
    }

    public function getStatus(): AbstractConsultationRequestStatus
    {
        return $this->status;
    }

    public function getLastChangeStatusDatetime(): \DateTime
    {
        return $this->lastChangeStatusDatetime;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

}
