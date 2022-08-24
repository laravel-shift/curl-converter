<?php

use Illuminate\Support\Facades\Route;

Route::resource('/', \App\Http\Controllers\ConverterController::class)->only('create', 'store');
