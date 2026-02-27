<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suivi_financiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->cascadeOnDelete()->unique();
            $table->decimal('total_du', 10, 2);
            $table->decimal('total_paye', 10, 2)->default(0);
            $table->decimal('solde_restant', 10, 2);
            $table->enum('statut', ['a_jour', 'en_retard', 'solde'])->default('en_retard');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suivi_financiers');
    }
};