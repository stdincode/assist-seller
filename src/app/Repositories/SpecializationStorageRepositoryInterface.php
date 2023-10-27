<?php

namespace App\Repositories;


use App\DataModels\Entities\Bags\SpecializationsBagInterface;
use App\DataModels\Entities\AbstractSpecialization;

interface SpecializationStorageRepositoryInterface
{
    public function getSpecializations(SpecializationsBagInterface $specializationsBag): SpecializationsBagInterface;

    public function getSpecializationById(int $id): ?AbstractSpecialization;

    public function createSpecialization(string $name): ?AbstractSpecialization;

    public function updateSpecialization(int $id, string $name): bool;

    public function deleteSpecialization(int $id);
}
