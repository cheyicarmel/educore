<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annee_academique_id')->constrained('annees_academiques')->cascadeOnDelete();
            $table->foreignId('serie_id')->constrained('series')->restrictOnDelete();
            $table->string('nom', 20);
            $table->string('niveau', 20);
            $table->enum('cycle', ['premier', 'second']);
            $table->timestamps();

            $table->unique(['nom', 'annee_academique_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};