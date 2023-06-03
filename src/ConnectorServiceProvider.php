<?php

namespace Seatplus\Connector;

use Illuminate\Support\ServiceProvider;

class ConnectorServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the JS & CSS,
        //$this->addPublications();

        // Add routes
        /*$this->loadRoutesFrom(__DIR__ . '/Http/routes.php');*/

        //Add Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');

        // Add translations
        //$this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'web');
    }

    public function register()
    {
        $this->mergeConfigurations();
    }

    private function mergeConfigurations()
    {

        $this->mergeConfigFrom(
            __DIR__.'/../config/connector.sidebar.php', 'package.sidebar'
        );

    }
}
