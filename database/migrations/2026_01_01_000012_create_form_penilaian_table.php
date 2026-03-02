<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_penilaian', function (Blueprint $table) {
            $table->id();
            $table->string('nama_form');
            $table->string('jenis_form', 50);
            $table->string('skim', 10)->nullable();
            $table->jsonb('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('tahun_ajaran')->nullable();
            $table->string('mongo_config_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['jenis_form', 'skim', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_penilaian');
    }
};
