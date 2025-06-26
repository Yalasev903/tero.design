<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class CleanupController
{
    public function cleanup(Request $request)
    {
        $path = public_path('multimedia');
        $previewMode = $request->boolean('preview', false);

        // Собираем все файлы
        $allFiles = collect(File::allFiles($path))->map(
            fn($file) => '/' . ltrim(Str::after($file->getRealPath(), public_path()), '/')
        );

        $used = collect();

        // home_projects_grid.media (JSON)
        if (Schema::hasTable('home_projects_grid')) {
            $media = DB::table('home_projects_grid')->pluck('media');
            $used = $used->merge(
                $media->flatMap(function ($json) {
                    $arr = json_decode($json, true);
                    return collect($arr)->only(['link', 'poster', 'links'])->flatten()->filter();
                })
            );
        }

        // projects.multimedia_grid (JSON)
        if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'multimedia_grid')) {
            $media = DB::table('projects')->pluck('multimedia_grid');
            $used = $used->merge(
                $media->flatMap(function ($json) {
                    $arr = json_decode($json, true);
                    return collect($arr)->flatten()->filter();
                })
            );
        }

        // showreel.poster, video, media.links[]
        if (Schema::hasTable('showreel')) {
            $used = $used
                ->merge(DB::table('showreel')->pluck('poster'))
                ->merge(DB::table('showreel')->pluck('video'))
                ->merge(
                    DB::table('showreel')->pluck('media')->flatMap(function ($json) {
                        $arr = json_decode($json, true);
                        return collect($arr['links'] ?? [])->pluck('link');
                    })
                );
        }

        // tbl_services.col_video
        if (Schema::hasTable('tbl_services') && Schema::hasColumn('tbl_services', 'col_video')) {
            $used = $used->merge(DB::table('tbl_services')->pluck('col_video'));
        }

        // tbl_workflow.col_poster, col_video
        if (Schema::hasTable('tbl_workflow')) {
            if (Schema::hasColumn('tbl_workflow', 'col_poster')) {
                $used = $used->merge(DB::table('tbl_workflow')->pluck('col_poster'));
            }
            if (Schema::hasColumn('tbl_workflow', 'col_video')) {
                $used = $used->merge(DB::table('tbl_workflow')->pluck('col_video'));
            }
        }

        // tbl_settings
        if (Schema::hasTable('tbl_settings')) {
            $used = $used
                ->merge(DB::table('tbl_settings')->pluck('col_showreel_poster'))
                ->merge(DB::table('tbl_settings')->pluck('col_showreel_video'))
                ->merge(DB::table('tbl_settings')->pluck('col_poster'))
                ->merge(DB::table('tbl_settings')->pluck('col_video'));
        }

        // tbl_map — col_marker
        if (Schema::hasTable('tbl_map') && Schema::hasColumn('tbl_map', 'col_marker')) {
            $used = $used->merge(DB::table('tbl_map')->pluck('col_marker'));
        }

        // pages.data — ищем multimedia в HTML
        if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'data')) {
            $rawPages = DB::table('pages')->pluck('data');
            $used = $used->merge(
                $rawPages->flatMap(function ($val) {
                    preg_match_all('/\/?multimedia\/[^\s"\']+/i', $val ?? '', $matches);
                    return collect($matches[0]);
                })
            );
        }

        // Приведение путей к виду /multimedia/...
        $used = $used
            ->filter()
            ->map(fn($val) => '/multimedia/' . ltrim(str_replace('\\', '/', $val), '/'))
            ->unique();

        // Сравнение
        $unused = $allFiles->diff($used)->values();

        // Папки
        $allDirs = collect(File::directories($path));
        $emptyDirs = $allDirs->filter(function ($dir) {
            return count(File::files($dir)) === 0 && count(File::directories($dir)) === 0;
        });

        // Режим предпросмотра
        if ($previewMode) {
            return response()->json([
                'total_files' => $allFiles->count(),
                'unused_files' => $unused,
                'unused_files_count' => $unused->count(),
                'total_dirs' => $allDirs->count(),
                'empty_dirs' => $emptyDirs,
                'empty_dirs_count' => $emptyDirs->count(),
            ]);
        }

        // Удаление
        foreach ($unused as $file) {
            File::delete(public_path($file));
        }

        foreach ($emptyDirs as $dir) {
            File::deleteDirectory($dir);
        }

        return response()->json([
            'message' => 'Очистка завершена',
            'deleted_files' => $unused->count(),
            'deleted_dirs' => $emptyDirs->count(),
        ]);
    }
}
