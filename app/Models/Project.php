<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'title',
        'text1',
        'text2',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'multimedia_grid',
    ];

    protected $casts = [
        'multimedia_grid' => 'array',
    ];
}
