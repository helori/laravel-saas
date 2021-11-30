# laravel-saas
Software as a Service scaffholding for Laravel based on Vue 3, Tailwindcss and Stripe. Inspired by Laravel Jetstream and Spark.

On a fresh Laravel 8 application, install the package running the following command. 
It will install all dependencies such as laravel/cashier, laravel/fortify, laravel/sanctum, ...

    composer require helori/laravel-saas

Publish the configuration files, migrations, routes, views and assets :

    php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
    php artisan vendor:publish --provider="Helori\LaravelSaas\SaasServiceProvider" --force

Run the migrations. Laravel default migrations and the package migrations will be run :

    php artisan migrate

Add Sanctum's middleware to your api middleware group within your application's app/Http/Kernel.php file:

    'api' => [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ...
    ],

Modify your .env file with the following configuration :

    SESSION_DRIVER=database
    SESSION_SECURE_COOKIE=true
    SAME_SITE_COOKIES=none

    SESSION_DOMAIN=".my-domain.test"
    SANCTUM_STATEFUL_DOMAINS="my-domain.test"

    STRIPE_KEY=stripe-key
    STRIPE_SECRET=stripe-secret

    CASHIER_CURRENCY=eur
    CASHIER_CURRENCY_LOCALE=fr_FR
    CASHIER_LOGGER=stack

Make sure your config/session.php has the following configuration :

    'same_site' => env('SAME_SITE_COOKIES', 'lax'),

Make sure your config/cors.php has the following configuration :

    'supports_credentials' => true,

Update your app\Providers\RouteServiceProvider.php as follows :

    public const HOME = '/app';

Install front-end dependencies :

    npm i @tailwindcss/forms @tailwindcss/typography axios tailwindcss vue@next vue-router@4 @heroicons/vue numeral moment --save-dev

Compile your assets :

    npm run watch

You can use the package seeders to seed your application with test data. 
To do so, edit your database/seeders/DatabaseSeeder.php like so :

    <?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;

    use Helori\LaravelSaas\Seeders\RootSeeder;
    use Helori\LaravelSaas\Seeders\UserSeeder;
    use Helori\LaravelSaas\Seeders\SubscriptionSeeder;


    class DatabaseSeeder extends Seeder
    {
        /**
         * Seed the application's database.
         *
         * @return void
         */
        public function run()
        {
            $this->call([
                RootSeeder::class,
                UserSeeder::class,
                SubscriptionSeeder::class,
            ]);
        }
    }

You can now refresh your database using :

    php artisan migrate:fresh --seed

