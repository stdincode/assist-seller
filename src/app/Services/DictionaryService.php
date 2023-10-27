<?php

namespace App\Services;

use App\DataModels\Entities\Bags\PlacesBag;
use App\DataModels\Entities\Bags\PlacesBagInterface;
use App\DataModels\Entities\Bags\SpecializationsBag;
use App\DataModels\Entities\Bags\SpecializationsBagInterface;
use App\DataModels\Entities\AbstractPlace;
use App\DataModels\Entities\AbstractSpecialization;
use App\Exceptions\Service\PlaceIdNotExistsException;
use App\Exceptions\Service\SpecializationIdNotExistsException;
use App\Repositories\PlaceStorageRepositoryInterface;
use App\Repositories\SpecializationStorageRepositoryInterface;

class DictionaryService implements DictionaryServiceInterface
{
    private PlaceStorageRepositoryInterface $placeStorageRepository;
    private SpecializationStorageRepositoryInterface $specializationStorageRepository;

    public function __construct(
        PlaceStorageRepositoryInterface $placeStorageRepository,
        SpecializationStorageRepositoryInterface $specializationStorageRepository
    )
    {
        $this->placeStorageRepository = $placeStorageRepository;
        $this->specializationStorageRepository = $specializationStorageRepository;
    }

    /**
     * @throws PlaceIdNotExistsException
     */
    public function checkExistsPlaces(array $placeIds): void
    {
        $existsPlaceIds = [];
        $existsPlaces = $this->getAllPlaces();
        foreach ($existsPlaces->getAll() as $place) {
            $existsPlaceIds[] = $place->getId();
        }

        foreach ($placeIds as $placeId) {
            if (!in_array($placeId, $existsPlaceIds)) throw PlaceIdNotExistsException::create($placeId);
        }
    }

    /**
     * @throws SpecializationIdNotExistsException
     */
    public function checkExistsSpecializations(array $specializationIds): void
    {
        $existsSpecializationIds = [];
        $existsSpecializations = $this->getAllPlaces();
        foreach ($existsSpecializations->getAll() as $specialization) {
            $existsSpecializationIds[] = $specialization->getId();
        }

        foreach ($specializationIds as $specializationId) {
            if (!in_array($specializationId, $existsSpecializationIds)) throw SpecializationIdNotExistsException::create($specializationId);
        }
    }

    public function getAllPlaces(): PlacesBagInterface
    {
        $placesEmptyBag = new PlacesBag();

        return $this->placeStorageRepository->getPlaces($placesEmptyBag);
    }

    public function createPlace(string $name): ?AbstractPlace
    {
        return $this->placeStorageRepository->createPlace($name);
    }

    public function updatePlace(int $id, string $name): bool
    {
        return $this->placeStorageRepository->updatePlace($id, $name);
    }

    public function deletePlace(int $id): bool
    {
        return $this->placeStorageRepository->deletePlace($id);
    }

    public function getAllSpecializations(): SpecializationsBagInterface
    {
        $specializationsEmptyBag = new SpecializationsBag();

        return $this->specializationStorageRepository->getSpecializations($specializationsEmptyBag);
    }

    public function createSpecialization(string $name): ?AbstractSpecialization
    {
        return $this->specializationStorageRepository->createSpecialization($name);
    }

    public function updateSpecialization(int $id, string $name): bool
    {
        return $this->specializationStorageRepository->updateSpecialization($id, $name);
    }

    public function deleteSpecialization(int $id): bool
    {
        return $this->specializationStorageRepository->deleteSpecialization($id);
    }
}
