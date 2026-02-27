<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coefficient_matieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matiere_id')->constrained('matieres')->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedTinyInteger('coefficient');
            $table->timestamps();

            $table->unique(['matiere_id', 'classe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coefficient_matieres');
    }
};