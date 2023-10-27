<?php

namespace App\Repositories;

use App\DataModels\Entities\Bags\PlacesBagInterface;
use App\DataModels\Entities\AbstractPlace;

interface PlaceStorageRepositoryInterface
{
    public function getPlaces(PlacesBagInterface $placesBag): PlacesBagInterface;

    public function getPlaceById(int $id): ?AbstractPlace;

    public function createPlace(string $name): ?AbstractPlace;

    public function updatePlace(int $id, string $name): bool;

    public function deletePlace(int $id);
}
