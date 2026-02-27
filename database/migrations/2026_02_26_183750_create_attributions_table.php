<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_id')->constrained('enseignants')->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('matiere_id')->constrained('matieres')->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->constrained('annees_academiques')->cascadeOnDelete();
            $table->boolean('est_prof_principal')->default(false);
            $table->date('date_attribution');
            $table->timestamps();

            $table->unique(['classe_id', 'matiere_id', 'annee_academique_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributions');
    }
};