<?php

namespace App\Repositories;

use App\DataModels\Entities\Bags\PlacesBagInterface;
use App\DataModels\Entities\Place;
use App\DataModels\Entities\AbstractPlace;
use Illuminate\Support\Facades\DB;

class PlaceStorageRepository implements PlaceStorageRepositoryInterface
{
    private string $placeTableName;

    public function __construct(string $placeTableName)
    {
        $this->placeTableName = $placeTableName;
    }

    public function getPlaces(PlacesBagInterface $placesBag): PlacesBagInterface
    {
        $places = DB::table($this->placeTableName)
            ->select([
                'id',
                'name',
            ])
            ->get();

        foreach ($places->all() as $place) {
            $placesBag->add(
                new Place(
                    id: $place->id,
                    name: $place->name
                )
            );
        }
        $placesBag->setTotal($places->count());

        return $placesBag;
    }

    public function getPlaceById(int $id): ?AbstractPlace
    {
        $place = DB::table($this->placeTableName)
            ->select([
                'id',
                'name',
            ])
            ->where(['id' => $id])
            ->first();

        if (!$place) return null;

        return new Place(
            id: $place->id,
            name: $place->name
        );
    }

    public function createPlace(string $name): AbstractPlace
    {
        $currentDateTime = new \DateTime();
        $id = DB::table($this->placeTableName)->insertGetId([
            'name' => $name,
        ]);

        return new Place(
            id: $id,
            name: $name
        );
    }

    public function updatePlace(
        int $id,
        string $name
    ): bool
    {
        return DB::table($this->placeTableName)
            ->where(['id' => $id])
            ->update([
                'name' => $name,
            ]);
    }

    public function deletePlace(int $id): bool
    {
        return DB::table($this->placeTableName)
            ->where(['id' => $id])
            ->delete();
    }
}
