<?php

namespace Brotzka\TranslationManager;

use Brotzka\TranslationManager\Module\Console\Commands\TranslationToDatabase;
use Brotzka\TranslationManager\Module\Console\Commands\TranslationToFile;
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

    private function registerCommands()
    {
    	if($this->app->runningInConsole()){
    		$this->commands([
                TranslationToDatabase::class,
                TranslationToFile::class
		    ]);
	    }
    }
}
