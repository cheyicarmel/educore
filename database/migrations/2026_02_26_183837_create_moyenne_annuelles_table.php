<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moyenne_annuelles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->cascadeOnDelete()->unique('uq_moy_ann_inscription');
            $table->decimal('valeur', 5, 2);
            $table->unsignedSmallInteger('rang')->nullable();
            $table->enum('decision', ['passant', 'doublant'])->nullable();
            $table->string('serie_attribuee', 5)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moyenne_annuelles');
    }
};