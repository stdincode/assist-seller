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
        Schema::create(config('database.table_names.consultations'), function (Blueprint $table) {
            $table->id();
            $table->integer('expert_consultation_request_id');
            $table->integer('consultation_request_id');
            $table->integer('telegram_menu_session_id');
            $table->string('expert_link', 1000)->nullable();
            $table->string('student_link', 1000)->nullable();
            $table->integer('status_id');
            $table->timestamp('last_change_status_datetime');
            $table->float('cost');
            $table->float('student_consultation_rating')->nullable();
            $table->float('student_call_quality_rating')->nullable();
            $table->string('student_comment')->nullable();
            $table->timestamp('student_comment_datetime')->nullable();
            $table->float('expert_call_quality_rating')->nullable();
            $table->string('expert_comment')->nullable();
            $table->timestamp('expert_comment_datetime')->nullable();
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
        Schema::dropIfExists(config('database.table_names.consultations'));
    }
};
