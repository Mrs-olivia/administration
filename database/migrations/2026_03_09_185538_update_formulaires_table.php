<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('formulaires', function (Blueprint $table) {
            // 1. Suppression de l'ancienne colonne
            $table->dropColumn('numero_enregistrement');
            $table->dropColumn('service_destinataire');

            // 2. Ajout des colonnes pour le numéro structuré
            $table->string('service_code', 10); // ex: 'DRH'
            $table->integer('annee');           // ex: 2026
            $table->integer('numero_ordre');
            $table->string('note')->nullable();     // ex: 42

            // 3. Ajout de la contrainte d'unicité (le "filet de sécurité")
            $table->unique(['service_code', 'annee', 'numero_ordre'], 'idx_unique_reference');

            // 4. Colonne pour le chemin du fichier (stockage du chemin uniquement)
            $table->string('fichier')->nullable();
            // 5. Colonne statut
            $table->unsignedTinyInteger('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sur PostgreSQL (table non vide), une colonne NOT NULL sans défaut échoue : les lignes
        // existantes recevraient NULL. On ajoute en nullable, on recopie service_code, puis on retire le nouveau schéma.
        Schema::table('formulaires', function (Blueprint $table) {
            $table->string('numero_enregistrement')->nullable();
            $table->string('service_destinataire')->nullable();
        });

        if (Schema::hasColumn('formulaires', 'service_code')) {
            DB::table('formulaires')->whereNull('service_destinataire')->update([
                'service_destinataire' => DB::raw('service_code'),
            ]);
        }

        Schema::table('formulaires', function (Blueprint $table) {
            $table->dropUnique('idx_unique_reference');
            $table->dropColumn(['service_code', 'annee', 'numero_ordre', 'note', 'fichier', 'status']);
        });
    }
};
