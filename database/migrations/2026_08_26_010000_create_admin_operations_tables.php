<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('service_code')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('mechanic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('vehicle');
            $table->string('service_type');
            $table->string('status')->default('pending');
            $table->dateTime('scheduled_at')->nullable();
            $table->decimal('labor_cost', 10, 2)->default(0);
            $table->decimal('parts_cost', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('category');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('reorder_level')->default(5);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('supplier')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
        Schema::dropIfExists('service_bookings');
    }
};
