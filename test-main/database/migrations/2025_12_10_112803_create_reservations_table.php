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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained();
            $table->foreignId('tenant_id')->constrained('users');
            $table->enum('status', ['pending', 'confirmed', 'canceled', 'completed'])->default('pending');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('duration_days');
            $table->decimal('total_price' , 10 , 2)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->enum('payment_status' , ['pending' , 'paid' , 'refunded'])->default('pending');
            $table->index(['property_id', 'check_in', 'check_out']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
