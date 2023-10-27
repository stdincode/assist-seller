<?php

namespace App\Services;

use App\DataModels\Entities\Bags\StudentsBag;
use App\DataModels\Entities\Bags\StudentsBagInterface;
use App\DataModels\Entities\AbstractStudent;
use App\Events\BlockedEvent;
use App\Exceptions\Service\StudentNotExistsException;
use App\Exceptions\Service\TelegramClientIdCreatedException;
use App\Exceptions\Service\StudentPhoneNumberCreatedException;
use App\Exceptions\Service\TelegramClientNotExistsException;
use App\Repositories\ExpertStorageRepositoryInterface;
use App\Repositories\StudentStorageRepositoryInterface;
use App\Repositories\TelegramStorageRepositoryInterface;

class StudentService implements StudentServiceInterface
{
    private StudentStorageRepositoryInterface $studentStorageRepository;
    private ExpertStorageRepositoryInterface $expertStorageRepository;
    private TelegramStorageRepositoryInterface $telegramStorageRepository;

    public function __construct(
        StudentStorageRepositoryInterface $studentStorageRepository,
        ExpertStorageRepositoryInterface $expertStorageRepository,
        TelegramStorageRepositoryInterface $telegramStorageRepository
    )
    {
        $this->studentStorageRepository = $studentStorageRepository;
        $this->expertStorageRepository = $expertStorageRepository;
        $this->telegramStorageRepository = $telegramStorageRepository;
    }

    public function getAllStudents(): StudentsBagInterface
    {
        $studentsEmptyBag = new StudentsBag();

        return $this->studentStorageRepository->getStudents(studentsBag: $studentsEmptyBag);
    }

    public function getStudent(int $id): ?AbstractStudent
    {
        return $this->studentStorageRepository->getStudentById(id: $id);
    }

    /**
     * @throws TelegramClientNotExistsException
     * @throws StudentPhoneNumberCreatedException
     * @throws TelegramClientIdCreatedException
     */
    public function createStudent(
        string  $firstName,
        int     $telegramClientId,
        ?int    $contactPhoneNumber
    ): ?AbstractStudent
    {
        $this->checkCreatedTelegramClientId($telegramClientId);
        if ($contactPhoneNumber) $this->checkCreatedStudentContactPhoneNumber($contactPhoneNumber);

        $telegramClient = $this->telegramStorageRepository->getTelegramClient($telegramClientId);
        if (!$telegramClient) throw TelegramClientNotExistsException::create();

        return $this->studentStorageRepository->createStudent(
            firstName: $firstName,
            telegramClient: $telegramClient,
            contactPhoneNumber: $contactPhoneNumber
        );
    }

    /**
     * @throws StudentPhoneNumberCreatedException
     * @throws StudentNotExistsException
     */
    public function updateStudent(
        int     $id,
        ?string $firstName,
        ?int    $contactPhoneNumber,
        ?bool   $isBlocked
    ): bool
    {

        $student = $this->studentStorageRepository->getStudentById($id);
        if (!$student) throw StudentNotExistsException::create();

        if ($contactPhoneNumber) $this->checkCreatedStudentContactPhoneNumber($contactPhoneNumber);

        if ($isBlocked === true) {
            $lastSession = $this->telegramStorageRepository->getLastTelegramSession($student->getTelegramClient());
            BlockedEvent::dispatch($lastSession->getTelegramChatId());
        }

        return $this->studentStorageRepository->updateStudent(
            id: $id,
            firstName: $firstName,
            contactPhoneNumber: $contactPhoneNumber,
            isBlocked: $isBlocked
        );
    }

    public function deleteStudent(int $id): bool
    {
        return $this->studentStorageRepository->deleteStudent(id: $id);
    }

    /**
     * @throws StudentPhoneNumberCreatedException
     */
    private function checkCreatedStudentContactPhoneNumber(int $contactPhoneNumber)
    {
        $student = $this->studentStorageRepository->getStudentByContactPhoneNumber($contactPhoneNumber);
        if ($student) throw StudentPhoneNumberCreatedException::create();
    }

    /**
     * @throws TelegramClientIdCreatedException
     */
    private function checkCreatedTelegramClientId(int $telegramClientId)
    {
        $student = $this->studentStorageRepository->getStudentByTelegramClientId($telegramClientId);
        if ($student) throw TelegramClientIdCreatedException::create();

        $expert = $this->expertStorageRepository->getExpertByTelegramClientId($telegramClientId);
        if ($expert) throw TelegramClientIdCreatedException::create();
    }
}
