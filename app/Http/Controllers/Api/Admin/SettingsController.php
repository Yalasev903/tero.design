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
        $settings = Setting::firstOrCreate([]);
        $map = Map::firstOrCreate([]);

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

        // $settings->col_jivochat      = $request->jivochat;
        // $settings->col_jivochat_id   = $request->jivochat_id;
        // $settings->col_email         = $request->email;
        // $settings->col_tel           = $request->tel;
        $settings->col_behance       = $request->behance;
        $settings->col_facebook      = $request->facebook;
        $settings->col_instagram     = $request->instagram;
        $settings->col_linkedin      = $request->linkedin;
        $settings->col_pinterest     = $request->pinterest;
        $settings->col_youtube       = $request->youtube;
        // $settings->col_seo_title     = $request->seo_title;
        // $settings->col_seo_description = $request->seo_description;
        // $settings->col_seo_keywords  = $request->seo_keywords;
        // $settings->col_showreel_poster = $request->poster;
        // $settings->col_showreel_video  = $request->video;
        $settings->save();

        $map = Map::first();
        $map->col_lat  = $request->lat;
        $map->col_lng  = $request->lng;
        $map->col_zoom = $request->zoom;
        $map->col_key  = $request->google_key;
        $map->save();

        return response()->noContent();
    }

    public function updateMap(Request $request)
    {
        $map = Map::firstOrCreate([]);

        $map->col_lat  = $request->lat ?? '';
        $map->col_lng  = $request->lng ?? '';
        $map->col_zoom = $request->zoom ?? '';
        $map->col_key  = $request->google_key ?? '';
        $map->save();

        return response()->noContent();
    }
}
