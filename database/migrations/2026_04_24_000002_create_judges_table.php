<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judges', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. J1, J2
            $table->string('name');
            $table->string('email')->unique();
            $table->string('specialty')->nullable();
            $table->string('judge_group')->nullable();
            $table->char('user_id', 36)->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('judges');
    }
};
