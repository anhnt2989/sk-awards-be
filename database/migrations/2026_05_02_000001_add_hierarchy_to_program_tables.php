<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_categories', function (Blueprint $table) {
            $table->string('parent_id')->nullable()->after('program_id');
            $table->foreign('parent_id')->references('id')->on('program_categories')->nullOnDelete();
        });

        Schema::table('program_criteria', function (Blueprint $table) {
            $table->string('category_id')->nullable()->after('program_id');
            $table->foreign('category_id')->references('id')->on('program_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('program_criteria', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::table('program_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
