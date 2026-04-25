<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uploaded_files', function (Blueprint $table) {
            $table->id();

            $table->string('fileable_type')->nullable();
            $table->string('fileable_id')->nullable();

            $table->index(['fileable_type', 'fileable_id']);

            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('disk')->default('public');
            $table->string('path')->unique();
            $table->string('original_name');
            $table->string('extension', 20)->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size');

            $table->timestamps();
        });
    }

    public function down(): void
    {
//        Schema::dropIfExists('uploaded_files');
    }
};
