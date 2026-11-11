<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_station_progress', function (Blueprint $table) {
            $table->renameColumn('cleared_at', 'finished_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_station_progress', function (Blueprint $table) {
            $table->renameColumn('finished_at', 'cleared_at');
        });
    }
};
