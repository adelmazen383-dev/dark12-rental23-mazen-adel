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
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_edited')->default(false)->after('message');
            $table->text('original_message')->nullable()->after('is_edited');
            $table->timestamp('edited_at')->nullable()->after('original_message');
            $table->boolean('is_deleted')->default(false)->after('edited_at');
            $table->timestamp('deleted_at')->nullable()->after('is_deleted');
            $table->string('deleted_by', 30)->nullable()->after('deleted_at'); // 'customer', 'investor', 'admin'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'is_edited',
                'original_message',
                'edited_at',
                'is_deleted',
                'deleted_at',
                'deleted_by',
            ]);
        });
    }
};
