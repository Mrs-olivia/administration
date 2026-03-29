<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulaire_reference_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('service_code', 10);
            $table->unsignedSmallInteger('annee');
            $table->unsignedInteger('last_num');
            $table->timestamps();
            $table->unique(['service_code', 'annee'], 'formulaire_ref_seq_service_annee_unique');
        });

        Schema::table('formulaires', function (Blueprint $table) {
            $table->string('origine_service_code', 10)->nullable()->after('service_code');
            $table->string('origine_reference', 48)->nullable()->after('origine_service_code');
        });

        if (Schema::hasTable('formulaires')) {
            $rows = DB::table('formulaires')
                ->select('service_code', 'annee', DB::raw('MAX(numero_ordre) as m'))
                ->groupBy('service_code', 'annee')
                ->get();

            $now = now();
            foreach ($rows as $row) {
                $serviceCode = $row->service_code;
                $annee = (int) $row->annee;
                $m = (int) $row->m;
                if ($serviceCode === null || $serviceCode === '' || $annee < 1 || $m < 1) {
                    continue;
                }
                DB::table('formulaire_reference_sequences')->insert([
                    'service_code' => $serviceCode,
                    'annee' => $annee,
                    'last_num' => $m,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('formulaires', function (Blueprint $table) {
            $table->dropColumn(['origine_service_code', 'origine_reference']);
        });

        Schema::dropIfExists('formulaire_reference_sequences');
    }
};
