<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // TODO what is this? sort it out
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';
