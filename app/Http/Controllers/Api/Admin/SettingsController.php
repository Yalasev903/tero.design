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
        $settings->update([
            'col_jivochat'       => $request->jivochat,
            'col_jivochat_id'    => $request->jivochat_id,
            'col_email'          => $request->email,
            'col_tel'            => $request->tel,
            'col_behance'        => $request->behance,
            'col_facebook'       => $request->facebook,
            'col_instagram'      => $request->instagram,
            'col_pinterest'      => $request->pinterest,
            'col_linkedin'       => $request->linkedin,
            'col_youtube'        => $request->youtube,
            'col_google_tm'      => $request->google_tm,
            'col_seo_title'      => $request->seo_title,
            'col_seo_description'=> $request->seo_description,
            'col_seo_keywords'   => $request->seo_keywords,
            'col_poster'         => $request->poster,
            'col_video'          => $request->video,
        ]);

        $map = Map::first();
        $map->update([
            'col_lat'     => $request->lat,
            'col_lng'     => $request->lng,
            'col_zoom'    => $request->zoom,
            'col_key'     => $request->google_key,
            'col_marker'  => $request->marker,
        ]);

        return response()->noContent();
    }
}
