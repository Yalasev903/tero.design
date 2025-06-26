<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Response;
use App\Models\Project;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        // Главная
        $urls[] = [
            'loc' => URL::to('/'),
            'priority' => '1.0',
        ];

        // Услуги
        $urls[] = [
            'loc' => URL::to('/services'),
            'priority' => '0.8',
        ];

        // Workflow
        $urls[] = [
            'loc' => URL::to('/workflow'),
            'priority' => '0.8',
        ];

        // Контакты
        $urls[] = [
            'loc' => URL::to('/contact'),
            'priority' => '0.5',
        ];

        // Проекты
        $projects = \App\Models\Project::all();
        foreach ($projects as $project) {
            $urls[] = [
                'loc' => URL::to('/projects/' . $project->id),
                'priority' => '0.7',
            ];
        }

        // Генерация XML
        $xml = view('sitemap.xml', ['urls' => $urls]);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
