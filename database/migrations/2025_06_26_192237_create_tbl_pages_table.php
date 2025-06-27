<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_pages', function (Blueprint $table) {
            $table->increments('col_id');
            $table->string('col_title')->index();
            $table->string('col_meta_title')->nullable();
            $table->string('col_meta_description')->nullable();
            $table->string('col_meta_keywords')->nullable();
            $table->text('col_text')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_pages');
    }
};
