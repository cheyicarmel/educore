<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('eleves')->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->constrained('annees_academiques')->cascadeOnDelete();
            $table->enum('statut', ['actif', 'passant', 'doublant'])->default('actif');
            $table->decimal('frais_annuels', 10, 2);
            $table->timestamps();

            $table->unique(['eleve_id', 'annee_academique_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};