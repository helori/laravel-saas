<?php

namespace App\Models;

use Helori\LaravelSaas\Models\User as UserSaas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\UserFactory;


class User extends UserSaas
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
