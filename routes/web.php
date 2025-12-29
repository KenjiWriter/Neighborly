<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

