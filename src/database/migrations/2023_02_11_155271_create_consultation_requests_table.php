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
        Schema::create(config('database.table_names.consultation_requests'), function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->integer('telegram_menu_session_id');
            $table->integer('place_id');
            $table->integer('specialization_id');
            $table->integer('student_id');
            $table->integer('status_id');
            $table->timestamp('last_change_status_datetime');
            $table->timestamp('consultation_datetime');
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
        Schema::dropIfExists(config('database.table_names.consultation_requests'));
    }
};
