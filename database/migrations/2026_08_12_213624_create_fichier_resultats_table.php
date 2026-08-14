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
    Schema::create('fichiers_resultats', function (Blueprint $table) {
        $table->id();
        $table->foreignId('filiere_id')->constrained('filieres');
        $table->string('annee_universitaire'); 
        $table->string('semestre'); 
        $table->string('nom_fichier');
        $table->string('chemin_fichier');
        $table->string('statut')->default('brouillon'); 
        $table->foreignId('importe_par')->constrained('users');
        $table->timestamp('date_import')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fichiers_resultats');
    }
};
