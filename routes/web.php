<?php

use Illuminate\Support\Facades\Route;
use Helori\LaravelSaas\Controllers\AppController;

Route::get('/', [AppController::class, 'home']);
