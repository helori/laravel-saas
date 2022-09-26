<?php

use Illuminate\Support\Facades\Route;
use Helori\LaravelSaas\Controllers\SaasController;

Route::group(['middleware' => ['web']], function ()
{
    Route::group(['middleware' => ['auth:sanctum', 'verified']], function ()
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

        Route::delete('/user', [SaasController::class, 'userDelete']);

        Route::get('/team/{teamId}/member', [SaasController::class, 'memberList']);
        Route::post('/team/{teamId}/member', [SaasController::class, 'memberCreate']);
        Route::put('/team/{teamId}/member/{memberId}', [SaasController::class, 'memberUpdate']);
        Route::delete('/team/{teamId}/member/{memberId}', [SaasController::class, 'memberDelete']);
        Route::get('/team/{teamId}/member/{memberId}/login', [SaasController::class, 'memberLogin']);
        Route::post('/team/{teamId}/member/{memberId}/invite', [SaasController::class, 'memberInvite']);

        Route::get('/payment-method-intent', [SaasController::class, 'paymentMethodIntent']);
        Route::get('/payment-method', [SaasController::class, 'paymentMethodRead']);
        Route::put('/payment-method', [SaasController::class, 'paymentMethodUpdate']);
        Route::delete('/payment-method', [SaasController::class, 'paymentMethodDelete']);
        
        Route::get('/products', [SaasController::class, 'products']);

        Route::get('/subscription', [SaasController::class, 'readSubscription']);
        Route::post('/subscription', [SaasController::class, 'createSubscription']);
        Route::delete('/subscription', [SaasController::class, 'deleteSubscription']);

        Route::get('/invoice', [SaasController::class, 'invoiceList']);

        Route::get('/promotion', [SaasController::class, 'readPromotion']);
        Route::post('/promotion', [SaasController::class, 'applyPromotion']);
    });
});

