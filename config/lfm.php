<?php

return [

    'use_package_routes' => true,

    'allow_private_folder' => false,
    'allow_shared_folder' => false, // отключаем лишнее

    /*
    |--------------------------------------------------------------------------
    | Папка в public, где будут файлы
    |--------------------------------------------------------------------------
    */
    'folder_categories' => [
        'file' => [
            'folder_name'  => '', // корень multimedia
            'startup_view' => 'list',
            'max_size'     => 50000,
            'thumb' => true,
            'thumb_width' => 80,
            'thumb_height' => 80,
            'valid_mime' => [
                'image/jpeg', 'image/png', 'image/gif',
                'video/mp4', 'video/webm', 'video/quicktime',
                'application/pdf', 'text/plain',
            ],
        ],
        'image' => [
            'folder_name'  => '', // корень multimedia
            'startup_view' => 'grid',
            'max_size'     => 50000,
            'thumb' => true,
            'thumb_width' => 80,
            'thumb_height' => 80,
            'valid_mime' => [
                'image/jpeg', 'image/png', 'image/gif',
            ],
        ],
    ],

    'paginator' => [
        'perPage' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Диск хранения — public => storage/app/public => /public
    |--------------------------------------------------------------------------
    */
    'disk' => 'multimedia',

    // Поведение при одинаковых файлах
    'rename_file' => false,
    'rename_duplicates' => false,
    'alphanumeric_filename' => false,
    'alphanumeric_directory' => false,
    'should_validate_size' => false,
    'should_validate_mime' => true,
    'over_write_on_duplicate' => false,

    'disallowed_mimetypes' => ['text/x-php', 'text/html'],
    'disallowed_extensions' => ['php', 'html'],

    'item_columns' => ['name', 'url', 'time', 'icon', 'is_file', 'is_image', 'thumb_url'],

    'should_create_thumbnails' => true,
    'thumb_folder_name' => 'thumbs',

    'raster_mimetypes' => [
        'image/jpeg', 'image/png',
    ],

    'thumb_img_width' => 200,
    'thumb_img_height' => 200,

    'file_type_array' => [
        'pdf' => 'Adobe Acrobat', 'doc' => 'Microsoft Word', 'docx' => 'Microsoft Word',
        'xls' => 'Excel', 'xlsx' => 'Excel', 'zip' => 'Archive', 'gif' => 'GIF Image',
        'jpg' => 'JPEG', 'jpeg' => 'JPEG', 'png' => 'PNG', 'mp4' => 'Video', 'webm' => 'WebM',
    ],

    'php_ini_overrides' => [
        'memory_limit' => '256M',
    ],
];
