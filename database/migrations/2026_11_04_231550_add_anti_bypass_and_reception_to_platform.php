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
        // 1. Add Reception Check-in & Anti-Bypass fields to Bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('reception_code', 20)->nullable()->after('reference_number')->index();
            $table->timestamp('reception_checked_in_at')->nullable()->after('confirmed_by');
            $table->timestamp('reception_checked_out_at')->nullable()->after('reception_checked_in_at');
            $table->foreignId('checked_in_by')->nullable()->after('reception_checked_out_at')->constrained('users')->nullOnDelete();
            $table->boolean('is_unregistered_deal')->default(false)->after('checked_in_by');
            $table->text('reception_notes')->nullable()->after('is_unregistered_deal');
        });

        // 2. Add Deal Negotiation, Survey, and Bypass Flags to Conversations
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('deal_status', 30)->default('negotiating')->after('status'); // negotiating, offer_sent, agreed, cancelled, caught_offline
            $table->decimal('offer_price', 10, 2)->nullable()->after('deal_status');
            $table->date('offer_checkin_date')->nullable()->after('offer_price');
            $table->date('offer_checkout_date')->nullable()->after('offer_checkin_date');
            $table->text('offer_notes')->nullable()->after('offer_checkout_date');
            $table->boolean('has_bypass_attempt')->default(false)->after('offer_notes');
            $table->string('bypass_attempt_reason')->nullable()->after('has_bypass_attempt');
            $table->string('client_survey_response', 30)->default('pending')->after('bypass_attempt_reason'); // pending, deal_done, no_deal
            $table->string('investor_survey_response', 30)->default('pending')->after('client_survey_response'); // pending, deal_done, no_deal
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn([
                'deal_status',
                'offer_price',
                'offer_checkin_date',
                'offer_checkout_date',
                'offer_notes',
                'has_bypass_attempt',
                'bypass_attempt_reason',
                'client_survey_response',
                'investor_survey_response',
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['checked_in_by']);
            $table->dropColumn([
                'reception_code',
                'reception_checked_in_at',
                'reception_checked_out_at',
                'checked_in_by',
                'is_unregistered_deal',
                'reception_notes',
            ]);
        });
    }
};
