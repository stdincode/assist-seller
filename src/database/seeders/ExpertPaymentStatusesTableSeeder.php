<?php

namespace Database\Seeders;

use App\Repositories\ExpertStorageRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpertPaymentStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = ExpertStorageRepository::EXPERT_PAYMENT_STATUSES;

        DB::table(config('database.table_names.expert_payment_statuses'))->insert($statuses);
    }
}
