<?php

namespace App\Services;

use App\DataModels\Entities\Bags\ExpertPaymentsBag;
use App\DataModels\Entities\Bags\ExpertPaymentsBagInterface;
use App\DataModels\Entities\Bags\ExpertPaymentStatusesBag;
use App\DataModels\Entities\Bags\ExpertPaymentStatusesBagInterface;
use App\DataModels\Entities\Bags\ExpertsBag;
use App\DataModels\Entities\Bags\ExpertsBagInterface;
use App\DataModels\Entities\AbstractExpert;
use App\DataModels\Entities\AbstractExpertPayment;
use App\Events\BlockedEvent;
use App\Events\ExpertPaymentStatusEvent;
use App\Events\ExpertVerificationEvent;
use App\Exceptions\Service\ExpertBalanceAtZeroException;
use App\Exceptions\Service\ExpertNotExistsException;
use App\Exceptions\Service\ExpertPaymentAlreadyUpdatedException;
use App\Exceptions\Service\ExpertPaymentNotExistsException;
use App\Exceptions\Service\ExpertPaymentStatusRequestExistsException;
use App\Exceptions\Service\NotEnoughExpertBalanceException;
use App\Exceptions\Service\TelegramClientIdCreatedException;
use App\Exceptions\Service\ExpertTelegramPhoneNumberCreatedException;
use App\Exceptions\Service\ExpertWhatsappPhoneNumberCreatedException;
use App\Exceptions\Service\StudentPhoneNumberCreatedException;
use App\Exceptions\Service\TelegramClientNotExistsException;
use App\Repositories\ExpertStorageRepository;
use App\Repositories\ExpertStorageRepositoryInterface;
use App\Repositories\StudentStorageRepositoryInterface;
use App\Repositories\TelegramStorageRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExpertService implements ExpertServiceInterface
{
    private ExpertStorageRepositoryInterface $expertStorageRepository;
    private StudentStorageRepositoryInterface $studentStorageRepository;
    private TelegramStorageRepositoryInterface $telegramStorageRepository;

    public function __construct(
        ExpertStorageRepositoryInterface $expertStorageRepository,
        StudentStorageRepositoryInterface $studentStorageRepository,
        TelegramStorageRepositoryInterface $telegramStorageRepository
    )
    {
        $this->expertStorageRepository = $expertStorageRepository;
        $this->studentStorageRepository = $studentStorageRepository;
        $this->telegramStorageRepository = $telegramStorageRepository;
    }

    public function getAllExperts(): ExpertsBagInterface
    {
        $expertsEmptyBag = new ExpertsBag();

        return $this->expertStorageRepository->getExperts($expertsEmptyBag);
    }

    public function getExpert(int $id): ?AbstractExpert
    {
        return $this->expertStorageRepository->getExpertById($id);
    }

    /**
     * @throws TelegramClientNotExistsException
     * @throws ExpertWhatsappPhoneNumberCreatedException
     * @throws ExpertTelegramPhoneNumberCreatedException
     * @throws TelegramClientIdCreatedException
     * @throws StudentPhoneNumberCreatedException
     */
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
    ): ?AbstractExpert
    {
        if ($uploadedAvatar) $avatar = $uploadedAvatar->store('experts/avatars');
        if ($uploadedVideo) $video = $uploadedVideo->store('experts/video');

        $this->checkCreatedTelegramClientId($telegramClientId);
        $this->checkCreatedExpertTelegramPhoneNumber($telegramPhoneNumber);
        if ($whatsappPhoneNumber) $this->checkCreatedExpertWhatsappPhoneNumber($whatsappPhoneNumber);
        $telegramClient = $this->telegramStorageRepository->getTelegramClient($telegramClientId);
        if (!$telegramClient) throw TelegramClientNotExistsException::create();

        return $this->expertStorageRepository->createExpert(
            firstName: $firstName,
            lastName: $lastName,
            patronymic: $patronymic,
            biography: $biography,
            telegramClient: $telegramClient,
            telegramPhoneNumber: $telegramPhoneNumber,
            whatsappPhoneNumber: $whatsappPhoneNumber,
            priceWorkHour: $priceWorkHour,
            requisites: $requisites,
            avatar: $avatar ?? null,
            video: $video ?? null,
            placeIds: $placeIds,
            specializationIds: $specializationIds
        );
    }

    /**
     * @throws ExpertTelegramPhoneNumberCreatedException
     * @throws ExpertWhatsappPhoneNumberCreatedException
     * @throws ExpertNotExistsException
     */
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
        ?array        $placeIds,
        ?array        $specializationIds
    ): bool
    {
        $expert = $this->expertStorageRepository->getExpertById($id);
        if (!$expert) throw ExpertNotExistsException::create();

        if ($telegramPhoneNumber) $this->checkCreatedExpertTelegramPhoneNumber(
            telegramPhoneNumber: $telegramPhoneNumber,
            expertId: $id
        );
        if ($whatsappPhoneNumber) $this->checkCreatedExpertWhatsappPhoneNumber(
            whatsappPhoneNumber: $whatsappPhoneNumber,
            expertId: $id
        );

        if ($uploadedAvatar) {
            $avatar = $uploadedAvatar->store('experts/avatars');
            if ($avatar) {
                $this->removeAvatar($id);
            }
        }

        if ($uploadedVideo) {
            $video = $uploadedVideo->store('experts/video');
            if ($video) {
                $this->removeVideo($id);
            }
        }

        if (is_bool($isVerification)) {
            $expertLastSession = $this->telegramStorageRepository->getLastTelegramSession($expert->getTelegramClient());
            $expertChatId = $expertLastSession->getTelegramChatId();
            ExpertVerificationEvent::dispatch($expertChatId, $isVerification);
        }

        if ($isBlocked === true) {
            $lastSession = $this->telegramStorageRepository->getLastTelegramSession($expert->getTelegramClient());
            BlockedEvent::dispatch($lastSession->getTelegramChatId());
        }

        return $this->expertStorageRepository->updateExpert(
            $id,
            $firstName,
            $lastName,
            $patronymic,
            $biography,
            $avatar ?? null,
            $video ?? null,
            $telegramPhoneNumber,
            $whatsappPhoneNumber,
            $priceWorkHour,
            $requisites,
            $balance,
            $isVerification,
            $isBlocked,
            $placeIds,
            $specializationIds
        );
    }

    public function deleteExpert(int $id): bool
    {
        return $this->expertStorageRepository->deleteExpert($id);
    }

    public function getExpertPayments(int $expertId): ExpertPaymentsBagInterface
    {
        $expertPaymentsEmptyBag = new ExpertPaymentsBag();

        return $this->expertStorageRepository->getExpertPayments($expertId, $expertPaymentsEmptyBag);
    }

    /**
     * @throws ExpertNotExistsException
     * @throws ExpertBalanceAtZeroException
     * @throws ExpertPaymentStatusRequestExistsException
     */
    public function createExpertPayment(int $expertId): ?AbstractExpertPayment
    {
        $expert = $this->getExpert($expertId);
        $defaultPaymentStatus = $this->getDefaultPaymentStatus();

        if (!$expert) throw ExpertNotExistsException::create();
        if ($expert->getBalance() <= 0) throw ExpertBalanceAtZeroException::create();
        $this->checkExistsExpertPaymentRequest($expert, $defaultPaymentStatus);

        return $this->expertStorageRepository->createExpertPayment(
            expertId: $expertId,
            amount: $expert->getBalance(),
            paymentStatusId: $defaultPaymentStatus['id'],
            paymentStatusName: $defaultPaymentStatus['name']
        );
    }

    /**
     * @throws NotEnoughExpertBalanceException
     * @throws ExpertNotExistsException
     * @throws ExpertPaymentNotExistsException
     * @throws ExpertPaymentAlreadyUpdatedException
     */
    public function updateExpertPayment(int $expertId, int $expertPaymentId, int $statusId): bool
    {
        $expertPayment = $this->expertStorageRepository->getExpertPayment($expertPaymentId);
        $expert = $this->expertStorageRepository->getExpertById($expertId);

        if (!$expert) throw ExpertNotExistsException::create();
        if (!$expertPayment) throw ExpertPaymentNotExistsException::create();
        if ($expertPayment->getUpdatedAt()) throw ExpertPaymentAlreadyUpdatedException::create();

        $successExpertPaymentStatus = ExpertStorageRepository::EXPERT_PAYMENT_STATUSES[2];
        $notSuccessExpertPaymentStatus = ExpertStorageRepository::EXPERT_PAYMENT_STATUSES[1];
        $expertLastSession = $this->telegramStorageRepository->getLastTelegramSession($expert->getTelegramClient());
        $expertChatId = $expertLastSession->getTelegramChatId();

        if ($statusId === $successExpertPaymentStatus['id']) {
            ExpertPaymentStatusEvent::dispatch($expertChatId, true);
            $this->deductFromBalance(expert: $expert, expertPayment: $expertPayment);
        } elseif ($statusId === $notSuccessExpertPaymentStatus['id']) {
            ExpertPaymentStatusEvent::dispatch($expertChatId, false);
        }

        return $this->expertStorageRepository->updateExpertPayment($expertPaymentId, $statusId);
    }

    public function getExpertPaymentStatuses(): ExpertPaymentStatusesBagInterface
    {
        $expertPaymentStatusesBag = new ExpertPaymentStatusesBag();

        return $this->expertStorageRepository->getExpertPaymentStatuses($expertPaymentStatusesBag);
    }

    protected function removeAvatar(string $id): void
    {
        $expert = $this->getExpert($id);
        if ($expert->getAvatar() && Storage::exists($expert->getAvatar())) {
            Storage::delete($expert->getAvatar());
        }
    }

    protected function removeVideo(string $id): void
    {
        $expert = $this->getExpert($id);
        if ($expert->getVideo() && Storage::exists($expert->getVideo())) {
            Storage::delete($expert->getVideo());
        }
    }

    /**
     * @throws NotEnoughExpertBalanceException
     */
    protected function deductFromBalance(
        AbstractExpert $expert,
        AbstractExpertPayment $expertPayment
    ): void
    {
        if ($expert->getBalance() < $expertPayment->getAmount()) {
            throw NotEnoughExpertBalanceException::create();
        }

        $this->expertStorageRepository->updateExpert(
            id: $expert->getId(),
            firstName: null,
            lastName: null,
            patronymic: null,
            biography: null,
            avatar: null,
            video: null,
            telegramPhoneNumber: null,
            whatsappPhoneNumber: null,
            priceWorkHour: null,
            requisites: null,
            balance: $expert->getBalance() - $expertPayment->getAmount(),
            isVerification: null,
            isBlocked: null,
            placeIds: null,
            specializationIds: null
        );
    }

    /**
     * @throws ExpertTelegramPhoneNumberCreatedException
     */
    private function checkCreatedExpertTelegramPhoneNumber(int $telegramPhoneNumber, ?int $expertId = null): void
    {
        $expert = $this->expertStorageRepository->getExpertByTelegramPhoneNumber($telegramPhoneNumber);
        if ($expert) {
            if ($expertId && $expert->getId() === $expertId) return;

            throw ExpertTelegramPhoneNumberCreatedException::create();
        }
    }

    /**
     * @throws ExpertWhatsappPhoneNumberCreatedException
     */
    private function checkCreatedExpertWhatsappPhoneNumber(int $whatsappPhoneNumber, ?int $expertId = null): void
    {
        $expert = $this->expertStorageRepository->getExpertByWhatsappPhoneNumber($whatsappPhoneNumber);
        if ($expert) {
            if ($expertId && $expert->getId() === $expertId) return;

            throw ExpertWhatsappPhoneNumberCreatedException::create();
        }
    }


    /**
     * @throws TelegramClientIdCreatedException
     * @throws StudentPhoneNumberCreatedException
     */
    private function checkCreatedTelegramClientId(int $telegramClientId): void
    {
        $expert = $this->expertStorageRepository->getExpertByTelegramClientId($telegramClientId);
        if ($expert) throw TelegramClientIdCreatedException::create();

        $student = $this->studentStorageRepository->getStudentByTelegramClientId($telegramClientId);
        if ($student) throw StudentPhoneNumberCreatedException::create();
    }

    private function getDefaultPaymentStatus(): array
    {
        $paymentStatusIndex = array_search('Запрос', array_column(ExpertStorageRepository::EXPERT_PAYMENT_STATUSES, 'name'));

        return ExpertStorageRepository::EXPERT_PAYMENT_STATUSES[$paymentStatusIndex];
    }

    private function getSuccessPaymentStatus(): array
    {
        $paymentStatusIndex = array_search('Проведена', array_column(ExpertStorageRepository::EXPERT_PAYMENT_STATUSES, 'name'));

        return ExpertStorageRepository::EXPERT_PAYMENT_STATUSES[$paymentStatusIndex];
    }

    /**
     * @throws ExpertPaymentStatusRequestExistsException
     */
    private function checkExistsExpertPaymentRequest(AbstractExpert $expert, array $defaultPaymentStatus): void
    {
        array_map(
        function (AbstractExpertPayment $expertPayment) use ($defaultPaymentStatus) {
                if ($expertPayment->getStatus()->getId() === $defaultPaymentStatus['id']) {
                    throw ExpertPaymentStatusRequestExistsException::create();
                }
            },
            $expert->getPaymentsBag()->getAll()
        );
    }

}
