<?php

namespace CalvinChiulele\MPesaMz\Providers;

use CalvinChiulele\MPesaMz\Services\MPesaMz;
use Illuminate\Support\ServiceProvider;

/**
 * @author Calvin Chiulele <cchiulele@protonmail.com>
 * @since 0.1.0
 */
class MPesaMzServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MPesaMz::class, function () {
            return new MPesaMz();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../../config/mpesa-config.php' => config_path('mpesa-config.php')]);
    }
}
