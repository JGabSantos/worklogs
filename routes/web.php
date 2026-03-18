<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TimeEntryController;
use App\Models\ActivityType;
use App\Models\Client;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/time-entries', [TimeEntryController::class, 'index'])
        ->name('time-entries.index');

    Route::get('/time-entries/create', function () {
        return view('time-entries.create', [
            'activityTypes' => ActivityType::where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'clients' => Client::where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    })->name('time-entries.create')
        ->middleware('permission:time-entries.create.own');


    Route::post('/time-entries', [TimeEntryController::class, 'store'])
        ->name('time-entries.store')
        ->middleware('permission:time-entries.create.own');

    Route::get('/time-entries/{id}/edit', [TimeEntryController::class, 'edit'])
        ->name('time-entries.edit')
        ->middleware('permission:time-entries.update.own');

    Route::put('/time-entries/{id}', [TimeEntryController::class, 'update'])
        ->name('time-entries.update')
        ->middleware('permission:time-entries.update.own');

    Route::delete('/time-entries/{id}', [TimeEntryController::class, 'destroy'])
        ->name('time-entries.destroy')
        ->middleware('permission:time-entries.delete.own');

    Route::get('/time-entries/{id}', [TimeEntryController::class, 'show'])
        ->name('time-entries.show')
        ->middleware('permission:time-entries.read.own');
});

require __DIR__ . '/settings.php';
