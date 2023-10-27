<?php

namespace App\Repositories;

use App\DataModels\Entities\Bags\ExpertPaymentsBagInterface;
use App\DataModels\Entities\Bags\ExpertPaymentStatusesBagInterface;
use App\DataModels\Entities\Bags\ExpertsBagInterface;
use App\DataModels\Entities\AbstractExpert;
use App\DataModels\Entities\AbstractExpertPayment;
use App\DataModels\Entities\AbstractTelegramClient;

interface ExpertStorageRepositoryInterface
{
    public function getExperts(ExpertsBagInterface $expertsBag): ExpertsBagInterface;

    public function getExpertById(int $id): ?AbstractExpert;

    public function getExpertByTelegramClientId(int $telegramClientId): ?AbstractExpert;

    public function getExpertByTelegramPhoneNumber(int $telegramPhoneNumber): ?AbstractExpert;

    public function getExpertByWhatsappPhoneNumber(int $whatsappPhoneNumber): ?AbstractExpert;

    public function createExpert(
        string                 $firstName,
        string                 $lastName,
        string                 $patronymic,
        string                 $biography,
        AbstractTelegramClient $telegramClient,
        int                    $telegramPhoneNumber,
        ?int                   $whatsappPhoneNumber,
        float                  $priceWorkHour,
        string                 $requisites,
        ?string                $avatar,
        ?string                $video,
        array                  $placeIds,
        array                  $specializationIds
    ): ?AbstractExpert;

    public function updateExpert(
        int     $id,
        ?string $firstName,
        ?string $lastName,
        ?string $patronymic,
        ?string $biography,
        ?string $avatar,
        ?string $video,
        ?int    $telegramPhoneNumber,
        ?int    $whatsappPhoneNumber,
        ?float  $priceWorkHour,
        ?string $requisites,
        ?float  $balance,
        ?bool   $isVerification,
        ?bool   $isBlocked,
        ?array  $placeIds,
        ?array  $specializationIds
    ): bool;

    public function deleteExpert(int $id): bool;

    public function getExpertPayments(int $expertId, ExpertPaymentsBagInterface $expertPaymentsBag): ExpertPaymentsBagInterface;

    public function getExpertPayment(int $expertPaymentId): ?AbstractExpertPayment;

    public function getLastWaitingExpertPayment(int $expertId): ?AbstractExpertPayment;

    public function createExpertPayment(
        int $expertId,
        float $amount,
        int $paymentStatusId,
        string $paymentStatusName
    ): ?AbstractExpertPayment;

    public function updateExpertPayment(int $id, int $statusId): bool;

    public function getExpertPaymentStatuses(ExpertPaymentStatusesBagInterface $expertPaymentStatusesBag): ExpertPaymentStatusesBagInterface;

    public function getRelevantExperts(ExpertsBagInterface $expertsBag, int $placeId, int $specializationId): ExpertsBagInterface;

}
