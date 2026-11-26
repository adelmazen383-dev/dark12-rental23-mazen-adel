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
        if (!Schema::hasTable('push_subscriptions')) {
            return;
        }

        Schema::table('push_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('push_subscriptions', 'subscriber_type')) {
                $table->string('subscriber_type', 20)->default('investor')->after('investor_id')->index();
            }
            if (!Schema::hasColumn('push_subscriptions', 'customer_phone')) {
                $table->string('customer_phone', 30)->nullable()->after('subscriber_type')->index();
            }
            if (!Schema::hasColumn('push_subscriptions', 'conversation_token')) {
                $table->string('conversation_token', 100)->nullable()->after('customer_phone')->index();
            }
            if (!Schema::hasColumn('push_subscriptions', 'booking_reference')) {
                $table->string('booking_reference', 50)->nullable()->after('conversation_token')->index();
            }
            if (!Schema::hasColumn('push_subscriptions', 'device_label')) {
                $table->string('device_label', 100)->nullable()->after('content_encoding');
            }
            if (!Schema::hasColumn('push_subscriptions', 'last_notified_at')) {
                $table->timestamp('last_notified_at')->nullable()->after('device_label');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('push_subscriptions')) {
            return;
        }

        Schema::table('push_subscriptions', function (Blueprint $table) {
            $columns = [
                'subscriber_type',
                'customer_phone',
                'conversation_token',
                'booking_reference',
                'device_label',
                'last_notified_at',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('push_subscriptions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
