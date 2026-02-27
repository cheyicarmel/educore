<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_eleves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('eleves')->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->constrained('annees_academiques')->cascadeOnDelete();
            $table->foreignId('moyenne_annuelle_id')->constrained('moyenne_annuelles')->cascadeOnDelete();
            $table->string('classe_depart', 20);
            $table->string('serie_depart', 5);
            $table->string('classe_arrivee', 20)->nullable();
            $table->string('serie_arrivee', 5)->nullable();
            $table->enum('statut', ['passant', 'doublant']);
            $table->timestamps();

            $table->unique(['eleve_id', 'annee_academique_id'], 'uq_hist_eleve_annee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_eleves');
    }
};