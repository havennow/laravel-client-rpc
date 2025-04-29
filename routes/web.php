<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Example\ExampleController::class, 'index']);
