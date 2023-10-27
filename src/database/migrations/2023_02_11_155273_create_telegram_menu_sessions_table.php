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
        Schema::create(config('database.table_names.telegram_menu_sessions'), function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_chat_id');
            $table->integer('telegram_client_id');
            $table->integer('telegram_menu_version_id');
            $table->timestamp('closed_at')->nullable()->default(null);
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
        Schema::dropIfExists(config('database.table_names.telegram_menu_sessions'));
    }
};
