<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->string('nom_etablissement');
            $table->string('slogan')->nullable();
            $table->string('ville');
            $table->string('pays')->default('Bénin');
            $table->string('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('telephone2')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();
            $table->string('logo')->nullable(); // chemin du fichier
            // Seuils mentions
            $table->decimal('seuil_insuffisant', 4, 1)->default(0);
            $table->decimal('seuil_passable',    4, 1)->default(10);
            $table->decimal('seuil_assez_bien',  4, 1)->default(12);
            $table->decimal('seuil_bien',        4, 1)->default(14);
            $table->decimal('seuil_tres_bien',   4, 1)->default(16);
            $table->decimal('seuil_excellent',   4, 1)->default(18);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres');
    }
};