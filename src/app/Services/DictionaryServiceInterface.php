<?php

namespace App\Services;

use App\DataModels\Entities\Bags\PlacesBagInterface;
use App\DataModels\Entities\Bags\SpecializationsBagInterface;
use App\DataModels\Entities\AbstractPlace;
use App\DataModels\Entities\AbstractSpecialization;

interface DictionaryServiceInterface
{
    public function checkExistsPlaces(array $placeIds): void;

    public function checkExistsSpecializations(array $specializationIds): void;

    public function getAllPlaces(): PlacesBagInterface;

    public function createPlace(string $name): ?AbstractPlace;

    public function updatePlace(int $id, string $name): bool;

    public function deletePlace(int $id): bool;

    public function getAllSpecializations(): SpecializationsBagInterface;

    public function createSpecialization(string $name): ?AbstractSpecialization;

    public function updateSpecialization(int $id, string $name): bool;

    public function deleteSpecialization(int $id): bool;
}
