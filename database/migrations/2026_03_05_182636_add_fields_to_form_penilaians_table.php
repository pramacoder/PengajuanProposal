<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('form_penilaian', function (Blueprint $table) {
            // fields: array of {label, type, description, required}
            // types: 'textarea' | 'integer_scale'
            $table->json('fields')->nullable()->after('config');
        });
    }

    public function down(): void
    {
        Schema::table('form_penilaian', function (Blueprint $table) {
            $table->dropColumn('fields');
        });
    }
};
