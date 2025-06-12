<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblService extends Model
{
    protected $table = 'tbl_services';

    protected $primaryKey = 'col_id';

    public $timestamps = true;

    protected $fillable = [
        'col_title',
        'col_description',
        'col_video',
        'position',
    ];
}
