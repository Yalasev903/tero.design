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
        Schema::create('tbl_settings', function (Blueprint $table) {
        $table->id();
        $table->boolean('col_jivochat')->default(false);
        $table->string('col_behance', 100)->nullable();
        $table->string('col_facebook', 100)->nullable();
        $table->string('col_instagram', 100)->nullable();
        $table->string('col_pinterest', 100)->nullable();
        $table->string('col_linkedin', 100)->nullable();
        $table->string('col_youtube', 100)->nullable();
        $table->string('col_email', 100)->nullable();
        $table->string('col_tel', 100)->nullable();
        $table->string('col_showreel_poster', 100)->nullable();
        $table->string('col_showreel_video', 100)->nullable();
        $table->text('col_privacy_policy_page')->nullable();
        $table->text('col_privacy_policy_modal')->nullable();
        $table->text('col_google_tm')->nullable();
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
