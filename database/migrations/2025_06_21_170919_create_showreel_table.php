<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('showreel', function (Blueprint $table) {
            $table->id();
            $table->string('poster')->nullable();
            $table->string('video')->nullable();
            $table->json('media')->nullable(); // на случай будущих расширений
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showreel');
    }
};
