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
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Features;
use Laravel\Cashier\Cashier;

/*use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;*/

use Helori\LaravelSaas\Models\User;
use Helori\LaravelSaas\Models\Team;


class SaasServiceProvider extends ServiceProvider
{
    public function register()
    {
        Cashier::ignoreMigrations();
    }
    
    public function boot()
	{
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'saas');
        $this->loadRoutesFrom(__DIR__.'/../routes/internal.php');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-saas-migrations');

        $this->publishes([
            __DIR__.'/../config/saas.php' => config_path('saas.php'),
            __DIR__.'/../config/auth.php' => config_path('auth.php'),
        ], 'laravel-saas-config');

        $this->publishes([
            __DIR__.'/../resources/css' => resource_path('css'),
            __DIR__.'/../resources/js' => resource_path('js'),
            __DIR__.'/../webpack.mix.js' => base_path('webpack.mix.js'),
            __DIR__.'/../tailwind.config.js' => base_path('tailwind.config.js'),
        ], 'laravel-saas-assets');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/helori/laravel-saas'),
        ], 'laravel-saas-views');

        $this->publishes([
            __DIR__.'/../routes/web.php' => base_path('routes/web.php'),
        ], 'laravel-saas-routes');

        $this->bootFortify();

        Cashier::useCustomerModel(Team::class);
        //Cashier::calculateTaxes();
	}

    protected function bootFortify()
    {
        /*Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);*/

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

        Fortify::authenticateUsing(function (Request $request){
            $user = User::where([
                'email' => trim($request->email),
                //'activated' => true,
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
    }
}
