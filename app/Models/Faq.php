<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'tbl_faq';
    protected $primaryKey = 'col_id';
    public $timestamps = true;

    protected $fillable = [
        'col_question',
        'col_answer',
    ];
}

