<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Showreel extends Model
{
    protected $table = 'showreel';

    protected $fillable = [
        'poster',
        'video',
        'media'
    ];

    protected $casts = [
        'media' => 'array'
    ];
}

