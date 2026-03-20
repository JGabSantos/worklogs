<?php

use App\Http\Controllers\TimeEntryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', 'verified')->group(function () {
    Route::view('/', 'dashboard')
        ->name('dashboard');

    Route::get('/time-entries', [TimeEntryController::class, 'index'])
        ->name('time-entries.index');

    Route::post('/time-entries', [TimeEntryController::class, 'store'])
        ->name('time-entries.store')
        ->middleware('permission:time-entries.create.own');

    Route::put('/time-entries/{id}', [TimeEntryController::class, 'update'])
        ->name('time-entries.update')
        ->middleware('permission:time-entries.update.own');

    Route::delete('/time-entries/{id}', [TimeEntryController::class, 'destroy'])
        ->name('time-entries.destroy')
        ->middleware('permission:time-entries.delete.own');
});

require __DIR__ . '/settings.php';
