<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan 'owner' ke daftar enum role
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM(
            'owner',
            'superadmin',
            'admin',
            'kaprod',
            'bahan',
            'print',
            'jahit',
            'qc',
            'finishing'
        ) NOT NULL DEFAULT 'admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan enum ke daftar awal tanpa 'owner'
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM(
            'superadmin',
            'admin',
            'kaprod',
            'bahan',
            'print',
            'jahit',
            'qc',
            'finishing'
        ) NOT NULL DEFAULT 'admin'");
    }
};