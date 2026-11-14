<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->boolean('admin_approved_override')->default(false)->after('status');
        });

        Schema::table('apartments', function (Blueprint $table) {
            $table->boolean('admin_approved_override')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn('admin_approved_override');
        });

        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn('admin_approved_override');
        });
    }
};
