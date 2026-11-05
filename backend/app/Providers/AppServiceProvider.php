<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Sanctum::ignoreMigrations(); // skema personal_access_tokens ada di migration milik proyek
    }

    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        Gate::define('view-reports', fn ($user): bool => $user->isAdmin());
        Gate::define('viewApiDocs', fn ($user = null): bool => true);

        RateLimiter::for('login', fn (Request $request): Limit => Limit::perMinute((int) (config('shop.rate_limit_login') ?? 60))
            ->by(strtolower((string) $request->input('email')).'|'.$request->ip()));

        RateLimiter::for('orders', fn (Request $request): Limit => Limit::perMinute((int) (config('shop.rate_limit_orders') ?? 10))
            ->by((string) ($request->user()?->id ?? $request->ip())));
    }
}
