<?php

use Illuminate\Support\Facades\Route;
use Helori\LaravelSaas\Controllers\SaasController;

Route::group(['middleware' => ['web']], function ()
{
    Route::group(['middleware' => 'auth:sanctum'], function ()
    {
        Route::get('/browser-session', [SaasController::class, 'readBrowserSession']);
        Route::delete('/browser-session', [SaasController::class, 'deleteBrowserSession']);//->middleware('password.confirm');

        Route::get('/api-token', [SaasController::class, 'readApiToken']);
        Route::post('/api-token', [SaasController::class, 'createApiToken']);
        Route::delete('/api-token', [SaasController::class, 'deleteApiToken']);

        Route::get('/team', [SaasController::class, 'readTeam']);
        Route::put('/team', [SaasController::class, 'updateTeam']);
        Route::get('/teams', [SaasController::class, 'listTeam']);
        Route::post('/team/switch/{teamId}', [SaasController::class, 'switchTeam']);

        Route::get('/team/{teamId}/member', [SaasController::class, 'memberList']);
        Route::put('/team/{teamId}/member/{memberId}', [SaasController::class, 'memberUpdate']);
        Route::delete('/team/{teamId}/member/{memberId}', [SaasController::class, 'memberDelete']);
        Route::get('/team/{teamId}/member/{memberId}/login', [SaasController::class, 'memberLogin']);

        Route::get('/card-intent', [SaasController::class, 'cardIntent']);
        Route::get('/card', [SaasController::class, 'readCard']);
        Route::put('/card', [SaasController::class, 'updateCard']);
        Route::delete('/card', [SaasController::class, 'deleteCard']);
        
        Route::get('/products', [SaasController::class, 'products']);

        Route::get('/subscription', [SaasController::class, 'readSubscription']);
        Route::post('/subscription', [SaasController::class, 'createSubscription']);
        Route::delete('/subscription', [SaasController::class, 'deleteSubscription']);

        Route::get('/invoice', [SaasController::class, 'invoiceList']);
    });
});

