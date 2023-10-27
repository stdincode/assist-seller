<?php

namespace App\Repositories;

use App\DataModels\Entities\AbstractConsultation;
use App\DataModels\Entities\AbstractConsultationRequest;
use App\DataModels\Entities\AbstractExpert;
use App\DataModels\Entities\AbstractExpertConsultationRequest;
use App\DataModels\Entities\AbstractPlace;
use App\DataModels\Entities\AbstractSpecialization;
use App\DataModels\Entities\AbstractStudent;
use App\DataModels\Entities\AbstractTelegramMenuSession;
use App\DataModels\Entities\AbstractWorkingDay;
use App\DataModels\Entities\AbstractWorkingHour;
use App\DataModels\Entities\Bags\ConsultationRequestsBagInterface;
use App\DataModels\Entities\Bags\ConsultationsBagInterface;
use App\DataModels\Entities\Bags\ExpertConsultationRequestsBagInterface;
use App\DataModels\Entities\Bags\WorkingDaysBagInterface;
use App\DataModels\Entities\Bags\WorkingHoursBagInterface;

interface ConsultationStorageRepositoryInterface
{
    public function getUpcomingConsultations(ConsultationsBagInterface $consultationsBag): ConsultationsBagInterface;

    public function updateConsultation(
        int $consultationId,
        ?string $expertLink = null,
        ?string $studentLink = null,
        ?int $statusId = null,
        ?float $studentConsultationRating = null,
        ?float $studentCallQualityRating = null,
        ?string $studentComment = null,
        ?\DateTime $studentCommentDatetime = null,
        ?float $expertCallQualityRating = null,
        ?string $expertComment = null,
        ?\DateTime $expertCommentDatetime = null
    ): bool;

    public function getConsultationRequestsByStudent(
        ConsultationRequestsBagInterface $consultationRequestsBag,
        AbstractStudent $student
    ): ConsultationRequestsBagInterface;

    public function getConsultationRequestById(int $id): ?AbstractConsultationRequest;

    public function createConsultationRequest(
        string $text,
        \DateTime $consultationDateTime,
        AbstractTelegramMenuSession $telegramMenuSession,
        AbstractPlace $place,
        AbstractSpecialization $specialization,
        AbstractStudent $student
    ): ?AbstractConsultationRequest;

    public function updateConsultationRequest(int $consultationRequestId, int $statusId): bool;

    public function updateExpertConsultationRequests(array $ids, int $statusId): bool;

    public function getExpertConsultationRequestsByConsultationRequestId(
        int $consultationRequestId,
        ExpertConsultationRequestsBagInterface $expertConsultationRequestsBag
    ): ExpertConsultationRequestsBagInterface;

    public function getExpertConsultationRequestById(int $id): ?AbstractExpertConsultationRequest;

    public function createExpertConsultationRequest(
        AbstractExpert $expert,
        AbstractConsultationRequest $consultationRequest
    ): ?AbstractExpertConsultationRequest;

    public function getConsultationsByExpertId(
        int $expertId,
        ConsultationsBagInterface $consultationsBag
    ): ConsultationsBagInterface;

    public function createConsultation(
        AbstractExpertConsultationRequest $expertConsultationRequest,
        AbstractConsultationRequest $consultationRequest,
        AbstractTelegramMenuSession $telegramMenuSession
    ): ?AbstractConsultation;

    public function getConsultationsByStudentId(
        int $studentId,
        ConsultationsBagInterface $consultationsBag
    ): ConsultationsBagInterface;

    public function getConsultationById(int $id): ?AbstractConsultation;

    public function changeStatusOfConsultation(int $consultationId, int $statusId): bool;

    public function getWorkingDays(\DateTime $beginDate = new \DateTime()): WorkingDaysBagInterface;

    public function getWorkingDayById(int $id): ?AbstractWorkingDay;

    public function getWorkingHours(
        \DateTime $selectedDate,
        \DateTime $beginTime = new \DateTime()
    ): WorkingHoursBagInterface;

    public function getWorkingHourById(
        int $id,
        \DateTime $selectedDate,
        \DateTime $beginTime = new \DateTime()
    ): ?AbstractWorkingHour;

}
