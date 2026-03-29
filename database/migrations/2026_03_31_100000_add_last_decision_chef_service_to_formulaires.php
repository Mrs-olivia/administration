<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formulaires', function (Blueprint $table) {
            $table->string('last_decision_chef_service_code', 10)->nullable()->after('chef_annotations_log');
        });

        // Décision supposée locale pour les dossiers déjà clos ou traités (évite de bloquer l’archivage existant).
        DB::table('formulaires')
            ->whereIn('status', [2, 3])
            ->update([
                'last_decision_chef_service_code' => DB::raw('service_code'),
            ]);
    }

    public function down(): void
    {
        Schema::table('formulaires', function (Blueprint $table) {
            $table->dropColumn('last_decision_chef_service_code');
        });
    }
};
