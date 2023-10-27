<?php

namespace Database\Seeders;

use App\Repositories\TelegramStorageRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TelegramMenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(config('database.table_names.telegram_menus'))->insert([
            [
                'id' => 1,
                'name' => TelegramStorageRepository::COMMON_MENU_NAME,
            ],
            [
                'id' => 2,
                'name' => TelegramStorageRepository::EXPERT_MENU_NAME,
            ],
            [
                'id' => 3,
                'name' => TelegramStorageRepository::STUDENT_MENU_NAME,
            ],
            [
                'id' => 4,
                'name' => TelegramStorageRepository::CONSULTATION_REQUEST_EXPERT_MENU_NAME,
            ],
        ]);
    }
}
