<?php

use App\Http\Controllers\EntryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('entries', EntryController::class);
Route::post('entries/generate', [EntryController::class, 'generate']);
Route::post('entries/delete-all', [EntryController::class, 'deleteAll']);
