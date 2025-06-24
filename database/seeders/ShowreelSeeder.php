<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Showreel;

class ShowreelSeeder extends Seeder
{
    public function run(): void
    {
        Showreel::updateOrCreate(
            ['id' => 1],
            [
                'poster' => 'showreel_2023/obl-2023_2.jpg',
                'video' => 'showreel/Showreel_2024_HD.mp4',
                'media' => json_encode([
                    'type' => 'video',
                    'poster' => 'showreel_2023/obl-2023_2.jpg',
                    'links' => [
                        ['link' => 'showreel/Showreel_2024_HD.mp4', 'mime' => 'video/mp4'],
                    ],
                    'width' => 1920,
                    'height' => 1080
                ], JSON_UNESCAPED_UNICODE)
            ]
        );
    }
}

