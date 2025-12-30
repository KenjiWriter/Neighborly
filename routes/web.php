<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::controller(\App\Http\Controllers\RegistrationOptionsController::class)->prefix('registration-options')->group(function () {
    Route::get('communities', 'communities')->name('registration.communities');
    Route::get('buildings', 'buildings')->name('registration.buildings');
    Route::get('units', 'units')->name('registration.units');
});


Route::middleware(['auth', 'verified'])->group(function () {
    // Account Verification Status Routes
    Route::get('/account/status', function () {
        return Inertia::render('Account/Status');
    })->name('account.status');

    Route::get('/account/pending', function () {
        return Inertia::render('Account/Pending');
    })->name('account.pending');

    Route::get('/account/rejected', function () {
        return Inertia::render('Account/Rejected');
    })->name('account.rejected');

    // Admin Verification Routes
    Route::middleware(['approved', 'role:admin'])->prefix('admin')->name('admin.')->group(function () { // Assuming 'role' middleware exists from Spatie
        Route::get('/users/pending', [\App\Http\Controllers\Admin\UserVerificationController::class, 'index'])->name('users.pending');
        Route::patch('/users/{user}/approve', [\App\Http\Controllers\Admin\UserVerificationController::class, 'approve'])->name('users.approve');
        Route::patch('/users/{user}/reject', [\App\Http\Controllers\Admin\UserVerificationController::class, 'reject'])->name('users.reject');
        
        // User Management
        Route::get('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('users.update');
    });

    // Approved Users Only
    Route::middleware(['approved'])->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('communities/primary', [\App\Http\Controllers\CommunityController::class, 'showPrimary'])->name('communities.show');

        // Maintenance
        Route::controller(\App\Http\Controllers\MaintenanceRequestController::class)->group(function () {
            Route::get('maintenance', 'index')->name('maintenance.index');
            Route::get('maintenance/create', 'create')->name('maintenance.create');
            Route::post('maintenance', 'store')->name('maintenance.store');
            Route::get('maintenance/{maintenanceRequest}', 'show')->name('maintenance.show');
            Route::patch('maintenance/{maintenanceRequest}/assign', 'assign')->name('maintenance.assign');
            Route::patch('maintenance/{maintenanceRequest}/status', 'updateStatus')->name('maintenance.status');
        });

        // Finance
        Route::controller(\App\Http\Controllers\FinanceController::class)->group(function () {
            Route::get('finances/overview', 'overview')->name('finances.overview');
        });

        // Documents
        Route::controller(\App\Http\Controllers\DocumentController::class)->group(function () {
            Route::get('documents', 'index')->name('documents.index');
            Route::get('documents/upload', 'create')->name('documents.create');
            Route::post('documents', 'store')->name('documents.store');
            Route::get('documents/{document}/download', 'download')->name('documents.download');
        });

        // Announcements
        Route::controller(\App\Http\Controllers\AnnouncementController::class)->group(function () {
            Route::get('announcements', 'index')->name('announcements.index');
            Route::get('announcements/{announcement}', 'show')->name('announcements.show');
        });

        // Polls
        Route::controller(\App\Http\Controllers\PollController::class)->group(function () {
            Route::get('polls', 'index')->name('polls.index');
            Route::get('polls/{poll}', 'show')->name('polls.show');
            Route::post('polls/{poll}/vote', 'vote')->name('polls.vote');
        });

        // Audit
        Route::get('audit', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');
    });
});


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

