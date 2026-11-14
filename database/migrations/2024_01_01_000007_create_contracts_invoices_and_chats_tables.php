<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Secret admin homepage curation column on apartments
        Schema::table('apartments', function (Blueprint $table) {
            $table->boolean('is_featured_on_homepage')->default(false)->after('is_active');
            $table->integer('homepage_order')->default(0)->after('is_featured_on_homepage');
        });

        // 2. Rental Contracts table
        Schema::create('rental_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number', 30)->unique();
            $table->foreignId('investor_id')->constrained()->onDelete('cascade');
            $table->foreignId('apartment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('unit_number', 20);
            $table->integer('floor_number');
            $table->string('tenant_name');
            $table->string('tenant_national_id', 20);
            $table->string('tenant_phone', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 12, 2);
            $table->decimal('insurance_amount', 12, 2)->default(0);
            $table->enum('payment_type', ['daily', 'monthly', 'quarterly', 'semi_annual', 'annual'])->default('monthly');
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->text('special_terms')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Invoices table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique();
            $table->foreignId('investor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('rental_contract_id')->nullable()->constrained('rental_contracts')->nullOnDelete();
            $table->string('client_name');
            $table->string('client_phone', 20)->nullable();
            $table->string('client_tax_number', 50)->nullable();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->enum('status', ['draft', 'issued', 'paid', 'cancelled'])->default('issued');
            $table->json('items')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Conversations table
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('investor_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->string('customer_token', 64)->unique();
            $table->string('subject')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });

        // 5. Messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->enum('sender_type', ['customer', 'investor', 'admin']);
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sender_name');
            $table->text('message');
            $table->boolean('is_read_by_customer')->default(false);
            $table->boolean('is_read_by_investor')->default(false);
            $table->boolean('is_read_by_admin')->default(false);
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('rental_contracts');
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn(['is_featured_on_homepage', 'homepage_order']);
        });
    }
};
