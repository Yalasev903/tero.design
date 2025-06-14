<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_workflow', function (Blueprint $table) {
            $table->id();
            $table->text('col_description')->nullable();
            $table->string('col_poster')->nullable();
            $table->string('col_video')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_workflow');
    }
};

