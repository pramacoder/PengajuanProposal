<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('user_identifier'); // NIM untuk mahasiswa, NIDN untuk dosen, dll
            $table->string('user_type'); // mahasiswa, dosen, reviewer, operator
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info'); // info, success, warning, danger, primary
            $table->json('data')->nullable(); // Data tambahan dalam format JSON
            $table->unsignedBigInteger('proposal_id')->nullable(); // ID proposal terkait (opsional)
            $table->timestamp('read_at')->nullable(); // Waktu notifikasi dibaca
            $table->timestamps();

            // Indexes
            $table->index(['user_identifier', 'user_type']);
            $table->index(['user_identifier', 'read_at']);
            $table->index('proposal_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
