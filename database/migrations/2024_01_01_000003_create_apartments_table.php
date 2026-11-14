<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained()->onDelete('cascade');
            $table->string('apartment_number', 10);
            $table->integer('floor_number');
            $table->text('description')->nullable();
            $table->integer('rooms_count')->default(1);
            $table->integer('living_rooms_count')->default(1);
            $table->integer('bathrooms_count')->default(1);
            $table->string('view_type')->nullable();
            $table->text('features')->nullable();
            $table->decimal('daily_price', 10, 2);
            $table->decimal('insurance_amount', 10, 2)->default(0);
            $table->decimal('extra_fees', 10, 2)->default(0);
            $table->string('extra_fees_description')->nullable();
            $table->time('checkin_time')->default('14:00');
            $table->time('checkout_time')->default('12:00');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['apartment_number', 'floor_number']);
        });

        Schema::create('apartment_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['image', 'video']);
            $table->string('file_path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartment_media');
        Schema::dropIfExists('apartments');
    }
};
