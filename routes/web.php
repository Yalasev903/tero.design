<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\DB;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/services', [ServiceController::class, 'index'])->name('services');

Route::get('/workflow', [WorkflowController::class, 'index'])->name('workflow');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');

Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/price', function () {

    $price = (object) [
        'description1' => '
            <p>You can often hear this from various studios, and it is correct in some way. And we have done this before. However, we realized that you, as a client, need to approximately understand our pricing and this will aid to save your time as well as ours and will help to move forward to discussing projects in detail. If you have any questions - do not hesitate to send us a message in the chat below. You can also always send your project for a more thorough estimation and we will try to reply to you as soon as possible.</p>
        ',
        'description2' => '
            <p>The prices indicated above cannot cover all aspects of the evaluation, however, they will be very close to the real ones. Moreover, during our conversation, we will be able to discuss the possibilities for a discount for you. Do not hesitate to contact us at any time! You will also find answers to frequently asked questions below.</p>
        ',
        'table' => [
            'interior' => [
                ['name' => 'View', 'value' => 0, 'max' => 6],
                ['name' => 'VR360', 'value' => 0, 'max' => 6],
                ['name' => 'Complexity', 'value' => 3, 'labels' => ['Low', 'Medium', 'High']]
            ],
            'exterior' => [
                ['name' => 'View', 'value' => 0, 'max' => 6],
                ['name' => 'VR360', 'value' => 0, 'max' => 6],
                ['name' => 'Complexity', 'value' => 3, 'labels' => ['Low', 'Medium', 'High']]
            ]
        ]
    ];

    $priceStr = [
        'interior' => ['View', 'VR360', 'Complexity'],
        'exterior' => ['View', 'VR360', 'Complexity']
    ];

    $coefficientData = [
        'interior' => ['View' => 1.0, 'VR360' => 1.2, 'Complexity' => 1.5],
        'exterior' => ['View' => 1.0, 'VR360' => 1.1, 'Complexity' => 1.3]
    ];

    $faq_list = DB::table('price_faq')->orderBy('position')->get();

    return view('price', [
        'price' => $price,
        'faq_list' => $faq_list,
        'priceStr' => $priceStr,
        'coefficientData' => $coefficientData
    ]);
});

Route::view('/admin', 'admin');
Route::get('/admin/{any}', fn () => view('admin'))->where('any', '.*');
