<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('user_identifier');
            $table->string('user_type', 50);
            $table->string('title');
            $table->text('message');
            $table->string('type', 50)->default('info');
            $table->jsonb('data')->nullable();
            $table->bigInteger('proposal_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_identifier', 'user_type']);
            $table->index(['user_identifier', 'read_at']);
            $table->index('proposal_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
