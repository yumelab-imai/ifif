<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // Laravelアプリケーションが生成する全てのURLで、安全な HTTPS を使用するように設定(redirect, route)
    public function boot(UrlGenerator $url)
    {
        // if (env("APP_ENV") == "production") {
        //     $url->forceScheme("https");
        // }
    }
}
