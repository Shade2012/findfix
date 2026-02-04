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
        Schema::create('founds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId(column: 'room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('location_hub_id')->nullable()->constrained('hubs')->cascadeOnDelete();
            $table->foreignId('found_category_id')->constrained('found_categories')->cascadeOnDelete();
            $table->foreignId('found_status_id')->constrained('found_statuses')->cascadeOnDelete();
            $table->string("found_description");
            $table->string("found_name");
            $table->string("found_phone_number")->nullable();
            $table->string("found_img")->nullable();
            $table->dateTime("found_date")->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('founds');
    }
};
