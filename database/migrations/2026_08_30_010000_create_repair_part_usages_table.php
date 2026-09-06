<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_part_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spare_part_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('mechanic_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_part_usages');
    }
};
