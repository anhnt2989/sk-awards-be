<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. P1, P2
            $table->string('name');
            $table->unsignedSmallInteger('year');
            $table->string('abbr', 10);
            $table->string('color', 20)->default('#1e3a5f');
            $table->string('status', 20)->default('active'); // active | completed
            $table->date('deadline')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('next_sub_id')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
