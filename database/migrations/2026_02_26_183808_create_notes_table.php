<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->cascadeOnDelete();
            $table->foreignId('matiere_id')->constrained('matieres')->cascadeOnDelete();
            $table->foreignId('enseignant_id')->constrained('enseignants')->cascadeOnDelete();
            $table->enum('type', ['interrogation1', 'interrogation2', 'interrogation3', 'devoir1', 'devoir2']);
            $table->unsignedTinyInteger('numero_semestre');
            $table->decimal('valeur', 4, 2);
            $table->timestamp('date_saisie')->useCurrent();
            $table->timestamps();

            $table->unique(['inscription_id', 'matiere_id', 'type', 'numero_semestre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};