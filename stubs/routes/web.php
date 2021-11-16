<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;

Route::group(['middleware' => ['web']], function ()
{
    Route::get('/', [AppController::class, 'home']);

    Route::group(['middleware' => 'auth:sanctum'], function ()
    {
        Route::get('/app', [AppController::class, 'app']);
    });
});

