<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->constrained('annees_academiques')->cascadeOnDelete();
            $table->foreignId('genere_par')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['semestriel', 'annuel']);
            $table->unsignedTinyInteger('numero_semestre')->nullable();
            $table->string('chemin_fichier_pdf', 255);
            $table->timestamp('date_generation')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
};