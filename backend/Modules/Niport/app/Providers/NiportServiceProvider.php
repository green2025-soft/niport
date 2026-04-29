<?php

namespace Modules\Niport\Providers;


use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class NiportServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Niport';

    protected string $nameLower = 'niport';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }














}
