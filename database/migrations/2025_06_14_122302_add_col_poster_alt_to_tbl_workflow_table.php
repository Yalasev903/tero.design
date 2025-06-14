<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_workflow', function (Blueprint $table) {
            $table->string('col_poster_alt')->nullable()->after('col_poster');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_workflow', function (Blueprint $table) {
            $table->dropColumn('col_poster_alt');
        });
    }
};

