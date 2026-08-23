<?php

namespace App\Providers;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
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
            blank(config('broadcasting.connections.reverb.secret'))
        ) {
            Log::critical(
                'REVERB_APP_SECRET is not set. The websockets trigger API is unauthenticated - set a real random secret.'
            );
        }

        if (!app()->runningInConsole()) {
            $this->bootWebApplication();
        }
    }

    private function bootWebApplication(): void
    {
        // The app enforces CSP via bepsvpt/secure-headers, not Laravel's own
        // nonce generator - csp_nonce('script') is what actually ends up in
        // the CSP header, so Vite's own tags must reuse that same value
        // rather than generating an independent nonce the header wouldn't allow.
        Vite::useCspNonce(csp_nonce('script'));

        if (Str::startsWith(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        $language = AppConfig::find('language');
        if ($language !== null && in_array($language->value(), ['en', 'nl'])) {
            app()->setLocale($language->value());
        }
    }
}
