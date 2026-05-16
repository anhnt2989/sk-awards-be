<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temp_scores', function (Blueprint $table) {
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->string('submission_id')->collation('utf8mb4_unicode_ci');
            $table->char('user_id', 36)->collation('utf8mb4_unicode_ci');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['submission_id', 'user_id']);
            $table->foreign('submission_id')->references('id')->on('submissions')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('temp_score_details', function (Blueprint $table) {
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('temp_score_id')->constrained('temp_scores')->cascadeOnDelete();
            $table->string('criterion_id')->collation('utf8mb4_unicode_ci');
            $table->unsignedSmallInteger('value')->default(0);

            $table->unique(['temp_score_id', 'criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temp_score_details');
        Schema::dropIfExists('temp_scores');
    }
};
