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

    // Декодируем JSON видео в массив, если есть
    public function getVideoAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    // При сохранении конвертируем массив обратно в JSON (если нужно)
    public function setVideoAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['video'] = json_encode($value);
        } else {
            $this->attributes['video'] = $value;
        }
    }
}
