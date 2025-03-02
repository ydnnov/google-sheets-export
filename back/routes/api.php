<?php

use App\Http\Controllers\EntryController;
use App\Http\Controllers\GoogleSheetsController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('entries', EntryController::class);
Route::post('entries/generate', [EntryController::class, 'generate']);
Route::post('entries/delete-all', [EntryController::class, 'deleteAll']);

Route::get('/settings/{key}', [SettingController::class, 'get']);
Route::post('/settings', [SettingController::class, 'set']);

// TODO: put this under auth protection, as well as other stuff
Route::post('/google-sheets/export', [GoogleSheetsController::class, 'export']);

Route::get('/fetch/{count?}', [GoogleSheetsController::class, 'readGoogleSheet']);
