<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->cascadeOnDelete();
            $table->foreignId('comptable_id')->constrained('comptables')->cascadeOnDelete();
            $table->decimal('montant', 10, 2);
            $table->date('date_paiement');
            $table->enum('mode_paiement', ['especes', 'virement', 'mobile_money', 'cheque']);
            $table->string('reference', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};