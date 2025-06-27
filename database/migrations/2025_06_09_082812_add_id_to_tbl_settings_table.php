<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Проверка: если колонки id НЕТ — добавим
        if (!Schema::hasColumn('tbl_settings', 'id')) {
            Schema::table('tbl_settings', function (Blueprint $table) {
                $table->id()->first();
            });
        }
    }

    public function down(): void
    {
        // Откат: удалим колонку id, если она есть
        if (Schema::hasColumn('tbl_settings', 'id')) {
            Schema::table('tbl_settings', function (Blueprint $table) {
                $table->dropColumn('id');
            });
        }
    }
};

