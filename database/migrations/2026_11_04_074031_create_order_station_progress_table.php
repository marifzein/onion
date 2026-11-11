<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_station_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('cabang_id')->constrained('cabangs')->cascadeOnDelete();
            $table->enum('division', [
                'bahan',
                'print',
                'jahit',
                'qc',
                'finishing',
            ]);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('completed_qty')->nullable();
            $table->string('current_activity')->nullable();
            $table->text('issue_note')->nullable();
            $table->boolean('is_cleared')->default(false);
            $table->dateTime('cleared_at')->nullable();
            $table->timestamps();

            // Mencegah record ganda per divisi di order yang sama
            $table->unique(['order_id', 'division']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_station_progress');
    }
};