<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(config('database.table_names.users'))->insert([
            'id' => 1000,
            'login' => 'root',
            'password' => Hash::make('qwe123123'),
            'created_at' => (new \DateTime('now')),
        ]);
    }
}
