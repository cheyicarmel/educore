<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moyenne_semestres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero_semestre');
            $table->decimal('valeur', 5, 2);
            $table->unsignedSmallInteger('rang')->nullable();
            $table->timestamps();

            $table->unique(['inscription_id', 'numero_semestre'], 'uq_moy_sem_inscription_semestre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moyenne_semestres');
    }
};