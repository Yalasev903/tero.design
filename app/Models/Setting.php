<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'tbl_settings';

    protected $fillable = [
        'col_jivochat', 'col_jivochat_id', 'col_email', 'col_tel',
        'col_behance', 'col_facebook', 'col_instagram', 'col_pinterest',
        'col_linkedin', 'col_youtube', 'col_google_tm',
        'col_seo_title', 'col_seo_description', 'col_seo_keywords',
        'col_poster', 'col_video'
];
}
