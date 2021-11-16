<?php

namespace Helori\LaravelSaas;

class Saas
{
    /**
     * The default user model class name.
     *
     * @var string
     */
    public static $userModel = 'App\\Models\\User';

    /**
     * The default team model class name.
     *
     * @var string
     */
    public static $teamModel = 'App\\Models\\Team';

    /**
     * Set the user model class name.
     *
     * @param  string  $model
     * @return void
     */
    public static function useUserModel($model)
    {
        static::$userModel = $model;
    }

    /**
     * Set the team model class name.
     *
     * @param  string  $model
     * @return void
     */
    public static function useTeamModel($model)
    {
        static::$teamModel = $model;
    }
}
