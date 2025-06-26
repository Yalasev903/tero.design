<?php

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CleanupController
{
    public function cleanup()
    {
        $path = public_path('multimedia');

        // Собираем все файлы из папки рекурсивно
        $allFiles = collect(File::allFiles($path))->map(
            fn($file) => '/' . Str::after($file->getRealPath(), public_path())
        );

        // Все используемые файлы из базы данных
        $used = collect()
            // home_projects_grid
            ->merge(DB::table('home_projects_grid')->pluck('poster'))
            ->merge(DB::table('home_projects_grid')->pluck('video'))

            // projects
            ->merge(DB::table('projects')->pluck('preview_image'))
            ->merge(DB::table('projects')->pluck('preview_video'))
            ->merge(DB::table('projects')->pluck('shutters_image_left'))
            ->merge(DB::table('projects')->pluck('shutters_image_right'))
            ->merge(DB::table('projects')->pluck('gallery_images')->flatMap(fn($val) => json_decode($val, true) ?? []))
            ->merge(DB::table('projects')->pluck('gallery_videos')->flatMap(fn($val) => json_decode($val, true) ?? []))

            // tbl_services
            ->merge(DB::table('tbl_services')->pluck('image'))
            ->merge(DB::table('tbl_services')->pluck('video'))

            // tbl_workflow
            ->merge(DB::table('tbl_workflow')->pluck('poster'))
            ->merge(DB::table('tbl_workflow')->pluck('video'))

            // showreel
            ->merge(DB::table('showreel')->pluck('poster'))
            ->merge(DB::table('showreel')->pluck('video'))

            // фильтруем пустые
            ->filter()
            ->map(fn($path) => '/' . ltrim($path, '/'));

        // Определяем неиспользуемые файлы
        $unused = $allFiles->diff($used)->values();

        // Удаляем неиспользуемые файлы
        foreach ($unused as $file) {
            File::delete(public_path($file));
        }

        // Удаляем пустые директории
        $deletedDirsCount = 0;
        $dirs = File::allDirectories($path);
        foreach (array_reverse($dirs) as $dir) {
            if (empty(File::files($dir)) && empty(File::directories($dir))) {
                File::deleteDirectory($dir);
                $deletedDirsCount++;
            }
        }

        // Возвращаем результат
        return response()->json([
            'message' => 'Очистка завершена',
            'deleted_files' => $unused->count(),
            'deleted_dirs' => $deletedDirsCount,
        ]);
    }
}

