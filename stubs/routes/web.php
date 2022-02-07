<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;

Route::group(['middleware' => ['web']], function ()
{
    Route::get('/', [AppController::class, 'home']);
    Route::get('/conditions-generales-de-vente-et-utilisation', [AppController::class, 'cgvu'])->name('cgvu');

    Route::group(['middleware' => 'auth:sanctum'], function ()
    {
        Route::get('/app', [AppController::class, 'app']);
    });
});

