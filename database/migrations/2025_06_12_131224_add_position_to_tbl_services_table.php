<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tbl_services', function (Blueprint $table) {
            $table->integer('position')->default(0)->after('col_video');
        });
    }

    public function down()
    {
        Schema::table('tbl_services', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
