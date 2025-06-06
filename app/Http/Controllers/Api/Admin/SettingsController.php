<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Map;

class SettingsController extends Controller
{
    public function show()
    {
        $settings = Setting::first();
        $map = Map::first();

        return response()->json([
            'jivochat'      => (bool) $settings->col_jivochat,
            'jivochat_id'   => $settings->col_jivochat_id,
            'email'         => $settings->col_email,
            'tel'           => $settings->col_tel,
            'behance'       => $settings->col_behance,
            'facebook'      => $settings->col_facebook,
            'instagram'     => $settings->col_instagram,
            'pinterest'     => $settings->col_pinterest,
            'linkedin'      => $settings->col_linkedin,
            'youtube'       => $settings->col_youtube,
            'google_tm'     => $settings->col_google_tm,
            'seo_title'     => $settings->col_seo_title,
            'seo_description' => $settings->col_seo_description,
            'seo_keywords'  => $settings->col_seo_keywords,
            'poster'        => $settings->col_poster,
            'video'         => $settings->col_video,
            'lat'           => $map->col_lat,
            'lng'           => $map->col_lng,
            'zoom'          => $map->col_zoom,
            'google_key'    => $map->col_key,
            'marker'        => $map->col_marker,
        ]);
    }

    public function update(Request $request)
    {
    $settings = Setting::first();

    if ($request->has('poster')) {
        $settings->col_poster = $request->poster;
    }

    if ($request->has('video')) {
        $settings->col_video = $request->video;
    }

    $settings->save();

    return response()->noContent();
    }
}
