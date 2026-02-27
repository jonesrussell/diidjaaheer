<?php

use App\Http\Controllers\CultureController;
use App\Http\Controllers\Dashboard\CulturalGroupController;
use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\GroupController;
use App\Http\Controllers\Dashboard\TeachingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicTeachingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', HomeController::class)->name('home');

Route::get('/welcome', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('welcome');

// Public culture pages
Route::get('/culture', [CultureController::class, 'index'])->name('culture.index');
Route::get('/culture/{slug}', [CultureController::class, 'show'])->name('culture.show');

// Public teaching pages
Route::get('/teachings', [PublicTeachingController::class, 'index'])->name('teachings.index');
Route::get('/teachings/{slug}', [PublicTeachingController::class, 'show'])->name('teachings.show');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('dashboard/cultural-groups', CulturalGroupController::class)->names('dashboard.cultural-groups');
    Route::resource('dashboard/events', EventController::class)->names('dashboard.events');
    Route::resource('dashboard/groups', GroupController::class)->names('dashboard.groups');
    Route::resource('dashboard/teachings', TeachingController::class)->names('dashboard.teachings');
});

require __DIR__.'/settings.php';
