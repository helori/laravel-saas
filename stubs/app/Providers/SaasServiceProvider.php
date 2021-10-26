<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Laravel\Fortify\Fortify;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;


class SaasServiceProvider extends ServiceProvider
{
    public function register()
    {
        
    }
    
    public function boot()
	{
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
	}
}
