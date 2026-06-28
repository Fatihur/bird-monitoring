<?php

use App\Http\Controllers\Api\CommandController;
use App\Http\Controllers\Api\UpdateController;
use Illuminate\Support\Facades\Route;

Route::post('/update', [UpdateController::class, 'store']);
Route::get('/command', [CommandController::class, 'index']);
Route::post('/buzzer/on', [CommandController::class, 'buzzerOn']);
Route::post('/buzzer/off', [CommandController::class, 'buzzerOff']);
Route::post('/command/ack', [CommandController::class, 'ack']);
