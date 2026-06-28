<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('user_type', 50);
            $table->bigInteger('user_id');
            $table->string('code', 6);
            $table->string('type', 10);
            $table->string('contact');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['user_type', 'user_id'], 'idx_vc_user');
            $table->index('code', 'idx_vc_code');
            $table->index('expires_at', 'idx_vc_expires');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};
