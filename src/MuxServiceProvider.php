<?php

namespace MartinBean\Laravel\Mux;

use Illuminate\Support\ServiceProvider;
use MuxPhp\Configuration;

class MuxServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->registerConfiguration();
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->configurePublishing();
        $this->configureRouting();
    }

    /**
     * Register the default Mux API configuration.
     */
    protected function registerConfiguration(): void
    {
        $this->app->singleton(Configuration::class, function (): Configuration {
            return Configuration::getDefaultConfiguration()
                ->setUsername($this->app->make('config')->get('mux.client_id'))
                ->setPassword($this->app->make('config')->get('mux.client_secret'));
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/mux.php', 'mux');
    }

    /**
     * Configure publishing for the package.
     */
    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/mux.php' => $this->app->configPath('mux.php'),
            ], 'mux-config');
        }
    }

    /**
     * Configure routing for the package.
     */
    protected function configureRouting(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
