<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviewers', function (Blueprint $table) {
            $table->bigIncrements('id_reviewer');
            $table->string('nama_reviewer');
            $table->string('no_hp_reviewer', 15);
            $table->string('email_reviewer')->unique();
            $table->string('password');
            $table->enum('role', ['reviewer'])->default('reviewer');
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviewers');
    }
};

