<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_judges', function (Blueprint $table) {
            $table->string('program_id');
            $table->string('judge_id');
            $table->timestamp('added_at')->useCurrent();

            $table->primary(['program_id', 'judge_id']);
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('judge_id')->references('id')->on('judges')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_judges');
    }
};
