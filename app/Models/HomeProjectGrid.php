<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeProjectGrid extends Model
{
    protected $table = 'home_projects_grid';

    protected $fillable = [
        'project_id',
        'media',
        'is_mobile',
        'row_number',
        'col_number',
    ];

    protected $casts = [
        'media' => 'array',
        'is_mobile' => 'boolean',
    ];

    public $timestamps = true;
}
