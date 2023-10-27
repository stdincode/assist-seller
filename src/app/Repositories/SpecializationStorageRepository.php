<?php

namespace App\Repositories;

use App\DataModels\Entities\Bags\SpecializationsBagInterface;
use App\DataModels\Entities\Specialization;
use App\DataModels\Entities\AbstractSpecialization;
use Illuminate\Support\Facades\DB;

class SpecializationStorageRepository implements SpecializationStorageRepositoryInterface
{
    private string $specializationTableName;

    public function __construct(string $specializationTableName)
    {
        $this->specializationTableName = $specializationTableName;
    }

    public function getSpecializations(SpecializationsBagInterface $specializationsBag): SpecializationsBagInterface
    {
        $specializations = DB::table($this->specializationTableName)
            ->select([
                'id',
                'name',
            ])
            ->get();

        foreach ($specializations->all() as $specialization) {
            $specializationsBag->add(
                new Specialization(
                    id: $specialization->id,
                    name: $specialization->name
                )
            );
        }
        $specializationsBag->setTotal($specializations->count());

        return $specializationsBag;
    }

    public function getSpecializationById(int $id): ?AbstractSpecialization
    {
        $specialization = DB::table($this->specializationTableName)
            ->select([
                'id',
                'name',
            ])
            ->where(['id' => $id])
            ->first();

        if (!$specialization) return null;

        return new Specialization(
            id: $specialization->id,
            name: $specialization->name
        );
    }

    public function createSpecialization(string $name): AbstractSpecialization
    {
        $id = DB::table($this->specializationTableName)->insertGetId([
            'name' => $name,
        ]);

        return new Specialization(
            id: $id,
            name: $name
        );
    }

    public function updateSpecialization(
        int $id,
        string $name
    ): bool
    {
        return DB::table($this->specializationTableName)
            ->where(['id' => $id])
            ->update([
                'name' => $name,
            ]);
    }

    public function deleteSpecialization(int $id): bool
    {
        return DB::table($this->specializationTableName)
            ->where(['id' => $id])
            ->delete();
    }
}
