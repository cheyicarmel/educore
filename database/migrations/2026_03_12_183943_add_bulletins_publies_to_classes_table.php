<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->boolean('bulletins_publies_s1')->default(false)->after('cycle');
            $table->boolean('bulletins_publies_s2')->default(false)->after('bulletins_publies_s1');
            $table->boolean('bulletins_publies_annuel')->default(false)->after('bulletins_publies_s2');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['bulletins_publies_s1', 'bulletins_publies_s2', 'bulletins_publies_annuel']);
        });
    }
};
