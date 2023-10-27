<?php

namespace App\Repositories;

use App\DataModels\Entities\Bags\StudentsBagInterface;
use App\DataModels\Entities\AbstractStudent;
use App\DataModels\Entities\AbstractTelegramClient;

interface StudentStorageRepositoryInterface
{
    public function getStudents(StudentsBagInterface $studentsBag): StudentsBagInterface;

    public function getStudentById(int $id): ?AbstractStudent;

    public function getStudentByContactPhoneNumber(int $contactPhoneNumber): ?AbstractStudent;

    public function getStudentByTelegramClientId(int $telegramClientId): ?AbstractStudent;

    public function createStudent(
        string                 $firstName,
        AbstractTelegramClient $telegramClient,
        ?int                   $contactPhoneNumber
    ): ?AbstractStudent;

    public function updateStudent(
        int     $id,
        ?string $firstName,
        ?int    $contactPhoneNumber,
        ?bool   $isBlocked
    ): bool;

    public function deleteStudent(int $id): bool;

}
