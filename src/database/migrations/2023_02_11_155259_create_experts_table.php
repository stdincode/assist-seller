<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('database.table_names.experts'), function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('patronymic');
            $table->string('biography');
            $table->string('avatar')->nullable();
            $table->string('video')->nullable();
            $table->integer('telegram_client_id')->unique();
            $table->string('telegram_phone_number')->unique();
            $table->string('whatsapp_phone_number')->nullable()->unique();
            $table->float('price_work_hour')->nullable();
            $table->string('requisites')->nullable();
            $table->float('balance');
            $table->boolean('is_verification')->nullable()->default(null);
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('database.table_names.experts'));
    }
};
