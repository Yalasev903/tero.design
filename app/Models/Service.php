<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'position',
        'title',
        'description',
        'video',
    ];

    public function getVideoAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }
}
