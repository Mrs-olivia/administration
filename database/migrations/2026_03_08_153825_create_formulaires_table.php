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
        Schema::create('formulaires', function (Blueprint $table) {
            $table->id();
            $table->string('numero_enregistrement');
            $table->string('expediteur');
            $table->text('objet');
            $table->string('type_document');
            $table->date('date_reception');
            $table->date('date_echeance')->nullable();
            $table->string('service_destinataire');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulaires');
    }
};
