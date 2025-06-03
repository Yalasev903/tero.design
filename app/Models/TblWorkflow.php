<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWorkflow extends Model
{
    protected $table = 'tbl_workflow';
    public $timestamps = false; // Если нет created_at/updated_at

    // Можно объявить fillable, если нужно
    protected $fillable = [
        'col_description',
        'col_poster',
        'col_video',
    ];
}
