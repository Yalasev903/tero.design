<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->boolean('jivochat')->default(false);
        $table->string('behance', 100)->nullable();
        $table->string('facebook', 100)->nullable();
        $table->string('instagram', 100)->nullable();
        $table->string('pinterest', 100)->nullable();
        $table->string('linkedin', 100)->nullable();
        $table->string('youtube', 100)->nullable();
        $table->string('email', 100)->nullable();
        $table->string('tel', 100)->nullable();
        $table->string('showreel_poster', 100)->nullable();
        $table->string('showreel_video', 100)->nullable();
        $table->text('privacy_policy_page')->nullable();
        $table->text('privacy_policy_modal')->nullable();
        $table->text('google_tm')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
