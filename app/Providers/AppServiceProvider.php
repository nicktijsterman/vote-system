<?php

namespace App\Providers;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Default value shipped in .env.example - anyone who deploys without
     * changing it leaves the websockets trigger API unauthenticated.
     */
    private const PLACEHOLDER_PUSHER_SECRET = 'enter-some-random-data-string-here';

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Deployments driven by .docker/entrypoint.sh never hit this: the
        // entrypoint generates a real secret before the app ever boots. This
        // is a safety net for bare-metal/non-Docker deployments.
        if (
            app()->environment('production') &&
            config('broadcasting.connections.pusher.secret') === self::PLACEHOLDER_PUSHER_SECRET
        ) {
            Log::critical(
                'PUSHER_APP_SECRET is still set to the example placeholder value from .env.example. ' .
                'The websockets trigger API is unauthenticated against anyone who knows this value - set a real random secret.'
            );
        }

        if (!app()->runningInConsole()) {
            $this->bootWebApplication();
        }
    }

    private function bootWebApplication(): void
    {
        if (Str::startsWith(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        $language = AppConfig::find('language');
        if ($language !== null && in_array($language->value(), ['en', 'nl'])) {
            app()->setLocale($language->value());
        }
    }
}
