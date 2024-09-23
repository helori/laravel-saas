<?php

namespace Helori\LaravelSaas;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Features;
use Laravel\Cashier\Cashier;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;

use Helori\LaravelSaas\Models\User;
use Helori\LaravelSaas\Models\Team;

use Helori\LaravelSaas\Commands\UpdateStripeProducts;


class SaasServiceProvider extends ServiceProvider
{
    public function register()
    {
        Cashier::calculateTaxes();
    }
    
    public function boot()
	{
        $this->bootPublishedFiles();
        $this->bootFortify();
        $this->bootCashier();

        $this->commands([
            UpdateStripeProducts::class,
        ]);
    }

    protected function bootPublishedFiles()
    {
        $this->loadMigrationsFrom(__DIR__.'/../stubs/database/migrations');
        $this->loadViewsFrom(__DIR__.'/../stubs/resources/views', 'saas');
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');

        // ---------------------------------------------------------------------
        //  Tags to publish only on install
        // ---------------------------------------------------------------------
        $this->publishes([
            __DIR__.'/../stubs/app' => app_path(),
        ], 'laravel-saas-app');

        $this->publishes([
            __DIR__.'/../stubs/database' => database_path(),
        ], 'laravel-saas-database');

        $this->publishes([
            __DIR__.'/../stubs/config/saas.php' => config_path('saas.php'),
            __DIR__.'/../stubs/config/auth.php' => config_path('auth.php'),
            __DIR__.'/../stubs/config/sanctum.php' => config_path('sanctum.php'), // to set the "user" guard
            __DIR__.'/../stubs/config/fortify.php' => config_path('fortify.php'), // to set the "user" guard
        ], 'laravel-saas-config');

        $this->publishes([
            __DIR__.'/../stubs/routes/web.php' => base_path('routes/web.php'),
        ], 'laravel-saas-routes');

        $this->publishes([
            __DIR__.'/../stubs/resources/views' => resource_path('views/vendor/saas'),
            __DIR__.'/../stubs/resources/css' => resource_path('css'),
            __DIR__.'/../stubs/resources/js' => resource_path('js'),
            __DIR__.'/../stubs/webpack.mix.js' => base_path('webpack.mix.js'),
            __DIR__.'/../stubs/tailwind.config.js' => base_path('tailwind.config.js'),
        ], 'laravel-saas-resources');
	}

    protected function bootCashier()
    {
        Cashier::useCustomerModel(Team::class);
        //Cashier::calculateTaxes();
    }

    protected function bootFortify()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(function (Request $request) {
            return view('saas::auth.login', [
                'email' => request()->input('email', ''),
            ]);
        });

        Fortify::registerView(function (Request $request) {
            return view('saas::auth.register');
        });

        Fortify::authenticateUsing(function (Request $request){
            $user = User::where([
                'email' => trim($request->email),
                'activated' => true,
            ])->first();

            if ($user && Hash::check(trim($request->password), $user->password)) {
                return $user;
            }
        });

        Fortify::authenticateThrough(function (Request $request) {
            return array_filter([
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
                Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]);
        });

        Fortify::requestPasswordResetLinkView(function (Request $request) {
            return view('saas::auth.forgot-password', [
                'email' => request()->input('email', ''),
            ]);
        });

        Fortify::resetPasswordView(function (Request $request) {
            return view('saas::auth.reset-password', [
                'token' => $request->route('token'),
                'request' => $request,
            ]);
        });

        Fortify::confirmPasswordView(function (Request $request) {
            return view('saas::auth.confirm-password', [
                'request' => $request,
            ]);
        });

        Fortify::twoFactorChallengeView(function (Request $request) {
            return view('saas::auth.two-factor-challenge', [
                'request' => $request,
            ]);
        });

        Fortify::verifyEmailView(function () {
            return view('saas::auth.verify-email');
        });
    }
}
