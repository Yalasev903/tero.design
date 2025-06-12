<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_services', function (Blueprint $table) {
            $table->id('col_id');
            $table->string('col_title', 255);
            $table->text('col_description');
            $table->string('col_video')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_services');
    }
};
