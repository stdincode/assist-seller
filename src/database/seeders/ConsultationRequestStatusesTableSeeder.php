<?php

namespace Database\Seeders;

use App\Repositories\ConsultationStorageRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsultationRequestStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = ConsultationStorageRepository::CONSULTATION_REQUEST_STATUSES;

        DB::table(config('database.table_names.consultation_request_statuses'))->insert($statuses);
    }
}
