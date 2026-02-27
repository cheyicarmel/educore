<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matieres', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->boolean('est_litteraire')->default(false);
            $table->boolean('est_scientifique')->default(false);
            $table->enum('sous_groupe', ['maths_physique', 'svt', 'litteraire', 'autre']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matieres');
    }
};