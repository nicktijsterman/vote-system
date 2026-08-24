<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /** @psalm-suppress UnusedClosureParam Both params are required by Auth::provider()'s
         * callback signature; VoterUserProvider needs neither. */
        Auth::provider('voter', fn ($app, array $config) => new VoterUserProvider());
    }
}
