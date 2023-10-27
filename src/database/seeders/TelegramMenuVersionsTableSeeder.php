<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class TelegramMenuVersionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createdAt = new \DateTime();

        DB::table(config('database.table_names.telegram_menu_versions'))->insert([
            [
                'id' => 1,
                'telegram_menu_id' => 1,
                'start_step_id' => Uuid::fromString('ce60b177-ed30-4ef8-5542-303a1de18bc5'),
                'created_at' => $createdAt,
            ],
            [
                'id' => 2,
                'telegram_menu_id' => 2,
                'start_step_id' => Uuid::fromString('7f78da48-3312-442e-aff8-46d1dfc2caae'),
                'created_at' => $createdAt,
            ],
            [
                'id' => 3,
                'telegram_menu_id' => 3,
                'start_step_id' => Uuid::fromString('5040cbf0-1c14-4480-bc34-e0b5e0677810'),
                'created_at' => $createdAt,
            ],
            [
                'id' => 4,
                'telegram_menu_id' => 4,
                'start_step_id' => Uuid::fromString('7cecad66-d605-4ecf-b138-ec8aa34218bc'),
                'created_at' => $createdAt,
            ],
        ]);
    }
}
