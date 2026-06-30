<?php

use App\Http\Controllers\Api\CommandController;
use App\Http\Controllers\Api\UpdateController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.token', 'throttle:120,1'])->group(function () {
    Route::post('/update', [UpdateController::class, 'store']);
    Route::get('/command', [CommandController::class, 'index']);
    Route::post('/relay/toggle', [CommandController::class, 'toggleRelay']);
    Route::post('/buzzer/toggle', [CommandController::class, 'toggleBuzzer']);
    Route::post('/pir/toggle', [CommandController::class, 'togglePir']);
    Route::post('/all/off', [CommandController::class, 'allOff']);
    Route::post('/command/ack', [CommandController::class, 'ack']);
    Route::delete('/history', [DashboardController::class, 'clearHistory']);
});
