<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->string('id')->primary();               // e.g. SK26-001
            $table->string('program_id');
            $table->string('name');
            $table->string('company');
            $table->char('submitter_id', 36);               // user.id (FK to users)
            $table->string('category_id');                 // e.g. C1
            $table->text('description')->nullable();
            $table->date('submitted_date');
            $table->string('status', 20)->default('pending'); // pending | approved | rejected
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('submitter_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['program_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
