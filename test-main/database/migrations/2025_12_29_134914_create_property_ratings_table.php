<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->between(1, 5);
            $table->text('review')->nullable();
            $table->timestamps();
            
            
            $table->unique(['reservation_id', 'user_id']);

            $table->unique(['property_id', 'user_id', 'reservation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_ratings');
    }
};