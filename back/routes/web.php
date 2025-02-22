<?php

use App\Http\Controllers\GoogleSheetsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
