<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moyenne_matieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->cascadeOnDelete();
            $table->foreignId('matiere_id')->constrained('matieres')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero_semestre');
            $table->decimal('moyenne_interrogations', 5, 2);
            $table->decimal('moyenne_generale', 5, 2);
            $table->decimal('moyenne_avec_coefficient', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['inscription_id', 'matiere_id', 'numero_semestre'], 'uq_moy_mat_inscription_matiere_semestre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moyenne_matieres');
    }
};