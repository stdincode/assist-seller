<?php

namespace App\Services;

use App\DataModels\Entities\Bags\ExpertPaymentsBagInterface;
use App\DataModels\Entities\Bags\ExpertPaymentStatusesBagInterface;
use App\DataModels\Entities\Bags\ExpertsBagInterface;
use App\DataModels\Entities\AbstractExpert;
use App\DataModels\Entities\AbstractExpertPayment;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ExpertServiceInterface
{
    public function getAllExperts(): ExpertsBagInterface;

    public function getExpert(int $id): ?AbstractExpert;

    public function createExpert(
        string        $firstName,
        string        $lastName,
        string        $patronymic,
        string        $biography,
        int           $telegramClientId,
        int           $telegramPhoneNumber,
        ?int          $whatsappPhoneNumber,
        float         $priceWorkHour,
        string        $requisites,
        ?UploadedFile $uploadedAvatar,
        ?UploadedFile $uploadedVideo,
        array         $placeIds,
        array         $specializationIds
    ): ?AbstractExpert;

    public function updateExpert(
        int           $id,
        ?string       $firstName,
        ?string       $lastName,
        ?string       $patronymic,
        ?string       $biography,
        ?UploadedFile $uploadedAvatar,
        ?UploadedFile $uploadedVideo,
        ?int          $telegramPhoneNumber,
        ?int          $whatsappPhoneNumber,
        ?float        $priceWorkHour,
        ?string       $requisites,
        ?float        $balance,
        ?bool         $isVerification,
        ?bool         $isBlocked,
        ?array         $placeIds,
        ?array         $specializationIds
    ): bool;

    public function deleteExpert(int $id): bool;

    public function getExpertPayments(int $expertId): ExpertPaymentsBagInterface;

    public function createExpertPayment(int $expertId): ?AbstractExpertPayment;

    public function updateExpertPayment(int $expertId, int $expertPaymentId, int $statusId): bool;

    public function getExpertPaymentStatuses(): ExpertPaymentStatusesBagInterface;

}
