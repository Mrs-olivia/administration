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
        Schema::table('formulaires', function (Blueprint $table) {
            // Rétablissement de l'ancienne colonne en cas de rollback
            $table->string('numero_enregistrement')->nullable();
            $table->string('service_destinataire');

            
            // Suppression des nouvelles colonnes
            $table->dropUnique('idx_unique_reference');
            $table->dropColumn(['service_code', 'annee', 'numero_ordre', 'fichier', 'status']);
        });
    }
};
