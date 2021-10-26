<?php

namespace Helori\LaravelSaas;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Helori\LaravelSaas\Models\Team;


class SaasServiceProvider extends ServiceProvider
{
    public function register()
    {
        Cashier::ignoreMigrations();
    }
    
    public function boot()
	{
		$this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'saas-migrations');

        $this->publishes([
            __DIR__.'/../config/saas.php' => config_path('saas.php'),
        ]);

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'saas');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/helori/laravel-saas'),
        ]);

        Cashier::useCustomerModel(Team::class);
        //Cashier::calculateTaxes();
	}
}
