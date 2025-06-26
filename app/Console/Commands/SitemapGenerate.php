<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Models\Project;

class SitemapGenerate extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Генерация sitemap.xml в public/';

    public function handle()
    {
        $urls = [];

        $urls[] = ['loc' => URL::to('/'), 'priority' => '1.0'];
        $urls[] = ['loc' => URL::to('/services'), 'priority' => '0.8'];
        $urls[] = ['loc' => URL::to('/workflow'), 'priority' => '0.8'];
        $urls[] = ['loc' => URL::to('/contact'), 'priority' => '0.5'];

        foreach (Project::all() as $project) {
            $urls[] = [
                'loc' => URL::to('/projects/' . $project->id),
                'priority' => '0.7',
            ];
        }

        $xml = view('sitemap.xml', compact('urls'))->render();
        file_put_contents(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap сгенерирован!');
    }
}
