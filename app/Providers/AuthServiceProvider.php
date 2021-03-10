<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\TokenGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define("can_close_tickets", function (User $user){
            return $user->user_type === "service_agent" || $user->user_type === "service_admin"
                ? Response::allow()
                : Response::deny('You are not authorized to perform this action.');
        });

        Gate::define("can_assign_tickets", function (User $user) {
            return $user->user_type === "service_admin"
                ? Response::allow()
                : Response::deny('You are not authorized to perform this action.');
        });
    }
}
