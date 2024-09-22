<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MapController;

Route::get('/', [MapController::class, 'index']);
Route::get('/maps2', [MapController::class, 'index2']);
