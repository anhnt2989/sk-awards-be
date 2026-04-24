<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->string('submission_id');
            $table->string('judge_id');
            $table->text('comment')->nullable();
            $table->boolean('is_submitted')->default(false);
            $table->timestamps();

            $table->unique(['submission_id', 'judge_id']);
            $table->foreign('submission_id')->references('id')->on('submissions')->cascadeOnDelete();
            $table->foreign('judge_id')->references('id')->on('judges')->cascadeOnDelete();
        });

        Schema::create('score_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('score_id')->constrained('scores')->cascadeOnDelete();
            $table->string('criterion_id');
            $table->unsignedSmallInteger('value')->default(0);

            $table->unique(['score_id', 'criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_details');
        Schema::dropIfExists('scores');
    }
};
