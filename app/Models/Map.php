<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $table = 'tbl_map';
    protected $fillable = [
        'col_lat', 'col_lng', 'col_zoom', 'col_key', 'col_marker'
];
}
