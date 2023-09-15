<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Product' => 'App\Policies\ModelPolicy',
        // 'App\Models\Role' => 'App\Policies\ModelPolicy',
        // 'App\Models\Category' => 'App\Policies\ModelPolicy',
    ];

    public function register()
    {
        parent::register();

        $this->app->bind('abilities', function () {
            return include base_path('data/abilities.php');
        });
    }

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // $user, $ability there variables already passed to function.
        Gate::before(function ($user, $ability) {
            // if return value will execute this condition if not mean won't return value will execute the code below it.
            if ($user->super_admin) {
                return true;
            }
        });

        foreach (app()->make('abilities') as $code => $label) {
            // $user laravel already passed for function and this represent the current user.
            Gate::define($code, function ($user) use ($code) {
                return $user->hasAbility($code);
            });
        }
    }
}
