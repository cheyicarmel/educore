<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('numero_matricule', 50)->unique();
            $table->date('date_naissance')->nullable();
            $table->enum('sexe', ['M', 'F']);
            $table->string('email_parent', 150)->nullable();
            $table->string('telephone_parent', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eleves');
    }
};