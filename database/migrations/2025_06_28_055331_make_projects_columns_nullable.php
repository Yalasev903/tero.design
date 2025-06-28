<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('meta_title', 266)->nullable()->change();
            $table->string('meta_description', 300)->nullable()->change();
            $table->string('meta_keywords', 260)->nullable()->change();
            $table->text('text1')->nullable()->change();
            $table->text('text2')->nullable()->change();
            $table->json('multimedia_grid')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('meta_title', 266)->nullable(false)->change();
            $table->string('meta_description', 300)->nullable(false)->change();
            $table->string('meta_keywords', 260)->nullable(false)->change();
            $table->text('text1')->nullable(false)->change();
            $table->text('text2')->nullable(false)->change();
            $table->json('multimedia_grid')->nullable(false)->change();
        });
    }
};
