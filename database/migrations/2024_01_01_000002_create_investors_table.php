<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('id_number', 20);
            $table->string('id_image')->nullable();
            $table->string('phone', 20);
            $table->string('whatsapp', 20);
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('iban', 34)->nullable();
            $table->string('license_number')->nullable();
            $table->string('license_name')->nullable();
            $table->string('license_image')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->timestamp('suspension_date')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};
