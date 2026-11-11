<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_so', 50)->unique();
            $table->foreignId('cabang_id')->constrained('cabangs')->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('order_title');
            $table->unsignedInteger('qty');
            $table->enum('satuan', ['pcs', 'stel'])->default('pcs');
            $table->string('fabric_type')->nullable();
            $table->string('order_type')->default('Atasan Fullprint');
            $table->date('target_deadline')->nullable();
            $table->string('spk_photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->string('current_status', 50)->default('draft_admin');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['cabang_id', 'current_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};