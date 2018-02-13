<?php

namespace Brotzka\TranslationManager;

use Illuminate\Support\ServiceProvider;

class TranslationManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->registerCommands();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Registering artisan commands
     */
    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Brotzka\TranslationManager\Module\Console\Commands\TranslationToDatabase::class,
                \Brotzka\TranslationManager\Module\Console\Commands\TranslationToFile::class
            ]);
        }
    }
}
