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
        Schema::create(config('database.table_names.telegram_client_step_messages'), function (Blueprint $table) {
            $table->id();
            $table->integer('telegram_client_step_id');
            $table->bigInteger('telegram_message_id');
            $table->string('message');
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
        Schema::dropIfExists(config('database.table_names.telegram_client_step_messages'));
    }
};
