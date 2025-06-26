<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ozdemir\VueFinder\VueFinder;
use League\Flysystem\Local\LocalFilesystemAdapter;

class VueFinderController extends Controller
{
    public function __invoke(Request $request)
    {
        $adapter = new LocalFilesystemAdapter(public_path('multimedia'));

        $finder = new VueFinder([
            'local' => $adapter,
        ]);

        // ⚠️ VueFinder сам вызывает ->send(), нам не нужно ничего возвращать
        $finder->init([
            'adapter' => 'local',
            'publicLinks' => [
                'local://' => asset('multimedia') . '/',
            ],
        ]);

        // Прекращаем выполнение, чтобы Laravel не начал второй ответ
        exit;
    }
}
