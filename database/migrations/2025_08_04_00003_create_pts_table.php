<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pts', function (Blueprint $table) {
            $table->bigIncrements('id_pt');
            $table->string('nama_pt');
            $table->string('no_hp_pt', 15);
            $table->string('email_pt')->unique();
            $table->string('password');
            $table->enum('role', ['operator'])->default('operator');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pts');
    }
};

