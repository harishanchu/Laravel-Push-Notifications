<?php namespace Anchu\Push;

use Illuminate\Support\ServiceProvider;
use Anchu\Push\Drivers\DriverFactory;

class PushServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('anchu/push');
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        // The driver factory is used to create the instance of actual real time
        // service provider class. We will inject the factory into the manager so that it may
        // make the instance while they are actually needed and not of before.
        $this->app->bindShared('push.factory', function($app)
        {
            return new DriverFactory();
        });

        // The Push manager is used to resolve various connections, since multiple
        // connections might be managed.
        $this->app['push'] = $this->app->share(function($app)
        {
            return new PushManager($app);
        });
        $this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Push', 'Anchu\Push\Facades\Push');
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('push');
	}

}