<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Laravel\Fortify\Fortify;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;

use Helori\LaravelSaas\Saas;
use App\Models\User;
use App\Models\Team;


class SaasServiceProvider extends ServiceProvider
{
    public function register()
    {
        
    }
    
    public function boot()
	{
        Saas::useUserModel(User::class);
        Saas::useTeamModel(Team::class);

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
	}
}
