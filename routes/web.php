<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('profile.edit');

Route::get('communities/primary', [\App\Http\Controllers\CommunityController::class, 'showPrimary'])
    ->middleware(['auth', 'verified'])
    ->name('communities.show');

// Maintenance Requests
Route::controller(\App\Http\Controllers\MaintenanceRequestController::class)->group(function () {
    Route::get('maintenance', 'index')->name('maintenance.index');
    Route::get('maintenance/create', 'create')->name('maintenance.create');
    Route::post('maintenance', 'store')->name('maintenance.store');
    Route::get('maintenance/{maintenanceRequest}', 'show')->name('maintenance.show');
    Route::patch('maintenance/{maintenanceRequest}/assign', 'assign')->name('maintenance.assign');
    Route::patch('maintenance/{maintenanceRequest}/status', 'updateStatus')->name('maintenance.status');
})->middleware(['auth', 'verified']);

// Finance & Documents
Route::controller(\App\Http\Controllers\FinanceController::class)->group(function () {
    Route::get('finances/overview', 'overview')->name('finances.overview');
})->middleware(['auth', 'verified']);

Route::controller(\App\Http\Controllers\DocumentController::class)->group(function () {
    Route::get('documents', 'index')->name('documents.index');
    Route::get('documents/upload', 'create')->name('documents.create');
    Route::post('documents', 'store')->name('documents.store');
    Route::get('documents/{document}/download', 'download')->name('documents.download');
})->middleware(['auth', 'verified']);

require __DIR__.'/settings.php';

Route::get('locale/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'pl'])) {
        abort(400);
    }
    session()->put('locale', $locale);
    return redirect()->back();
})->name('locale.switch');

if (app()->environment('local')) {
    Route::get('/playground', function () {
        return \Inertia\Inertia::render('Playground');
    })->name('playground');
}

