<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('prof_principal_id')->nullable()->after('serie_id')->constrained('enseignants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['prof_principal_id']);
            $table->dropColumn('prof_principal_id');
        });
    }
};