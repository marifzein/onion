<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabrics', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); // Contoh: Ntech Dibalik
            $table->string('gramasi', 50)->nullable(); // Contoh: 130 gsm
            $table->text('description')->nullable(); // Karakteristik bahan / catatan potong
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabrics');
    }
};