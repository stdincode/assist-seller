<?php

namespace App\Services;

use App\DataModels\Entities\Bags\StudentsBagInterface;
use App\DataModels\Entities\AbstractStudent;

interface StudentServiceInterface
{
    public function getAllStudents(): StudentsBagInterface;

    public function getStudent(int $id): ?AbstractStudent;

    public function createStudent(
        string  $firstName,
        int     $telegramClientId,
        ?int    $contactPhoneNumber
    ): ?AbstractStudent;

    public function updateStudent(
        int     $id,
        ?string $firstName,
        ?int    $contactPhoneNumber,
        ?bool   $isBlocked
    ): bool;

    public function deleteStudent(int $id): bool;

}
