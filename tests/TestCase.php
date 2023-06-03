<?php

namespace Seatplus\Connector\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;
use Seatplus\Auth\AuthenticationServiceProvider;
use Seatplus\Auth\Models\User;
use Seatplus\Connector\ConnectorServiceProvider;
use Seatplus\Eveapi\EveapiServiceProvider;
use Seatplus\Web\Http\Middleware\Authenticate;

class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => match (true) {
                Str::startsWith($modelName, 'Seatplus\Auth') => 'Seatplus\\Auth\\Database\\Factories\\'.class_basename($modelName).'Factory',
                Str::startsWith($modelName, 'Seatplus\Eveapi') => 'Seatplus\\Eveapi\\Database\\Factories\\'.class_basename($modelName).'Factory',
                Str::startsWith($modelName, 'Seatplus\Connector') => 'Seatplus\\Connector\\Database\\Factories\\'.class_basename($modelName).'Factory',
                default => dd('no match for '.$modelName)
            }
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ConnectorServiceProvider::class,
            AuthenticationServiceProvider::class,
            EveapiServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config(['app.debug' => true]);

        $app['router']->aliasMiddleware('auth', Authenticate::class);

        // Use test User model for users provider
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('cache.prefix', 'seatplus_tests---');

        //Setup Inertia for package development
        /*config()->set('inertia.testing.page_paths', array_merge(
            config()->get('inertia.testing.page_paths', []),
            [
                realpath(__DIR__ . '/../src/resources/js/Pages'),
                realpath(__DIR__ . '/../src/resources/js/Shared')
            ],
        ));*/
    }

    /**
     * Resolve application HTTP Tribe implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        //$app->singleton('Illuminate\Contracts\Http\Tribe', Tribe::class);
    }
}
