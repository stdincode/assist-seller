<?php

namespace Database\Seeders;

use App\Repositories\ConsultationStorageRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpertConsultationRequestStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = ConsultationStorageRepository::EXPERT_CONSULTATION_REQUEST_STATUSES;

        DB::table(config('database.table_names.expert_consultation_request_statuses'))->insert($statuses);
    }
}
