<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formulaires', function (Blueprint $table) {
            $table->unsignedInteger('transfers_count')->default(0)->after('origine_reference');
            $table->timestamp('first_transferred_at')->nullable()->after('transfers_count');
            $table->timestamp('last_transferred_at')->nullable()->after('first_transferred_at');
        });
    }

    public function down(): void
    {
        Schema::table('formulaires', function (Blueprint $table) {
            $table->dropColumn(['transfers_count', 'first_transferred_at', 'last_transferred_at']);
        });
    }
};
