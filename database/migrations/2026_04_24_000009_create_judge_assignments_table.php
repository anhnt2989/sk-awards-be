<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judge_assignments', function (Blueprint $table) {
            $table->string('submission_id');
            $table->string('judge_id');
            $table->timestamp('assigned_at')->useCurrent();

            $table->primary(['submission_id', 'judge_id']);
            $table->foreign('submission_id')->references('id')->on('submissions')->cascadeOnDelete();
            $table->foreign('judge_id')->references('id')->on('judges')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('judge_assignments');
    }
};
