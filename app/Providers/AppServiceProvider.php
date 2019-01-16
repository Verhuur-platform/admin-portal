<?php

namespace App\Providers;

use App\User;
use App\Models\Werkpunten;
use App\Observers\{WerkpuntObserver, UserObserver};
use Spatie\BladeX\Facades\BladeX;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Model observers registration
        Werkpunten::observe(WerkpuntObserver::class);
        User::observe(UserObserver::class);

        // Blade registrations
        BladeX::component('components.*');
        BladeX::component('helpdesk.components.*');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
