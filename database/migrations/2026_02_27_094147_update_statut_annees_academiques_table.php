<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annees_academiques', function (Blueprint $table) {
            $table->dropColumn('est_active');
        });

        Schema::table('annees_academiques', function (Blueprint $table) {
            $table->enum('statut', ['active', 'a_venir', 'terminee'])
                  ->default('a_venir')
                  ->after('date_fin');
        });

        // L'année existante était active, on la marque comme telle
        DB::table('annees_academiques')->update(['statut' => 'active']);
    }

    public function down(): void
    {
        Schema::table('annees_academiques', function (Blueprint $table) {
            $table->dropColumn('statut');
        });

        Schema::table('annees_academiques', function (Blueprint $table) {
            $table->boolean('est_active')->default(false)->after('date_fin');
        });

        // Restaurer l'ancienne valeur
        DB::table('annees_academiques')->update(['est_active' => true]);
    }
};