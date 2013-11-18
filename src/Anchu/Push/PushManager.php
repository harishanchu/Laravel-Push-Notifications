<?php namespace Anchu\Push;

use Anchu\Push\Services\ServiceFactory;

class PushManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The database connection factory instance.
     *
     * @var \Anchu\Push\Services\ServiceFactory
     */
    protected $factory;

    /**
     * The active services instances.
     *
     * @var array
     */
    protected $services = array();

    /**
     * Create a new Push manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app, ServiceFactory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
    }

    /**
     * Get the default service name.
     *
     * @return string
     */
    public function getDefaultService()
    {
        return $this->app['config']['push::default'];
    }

    /**
     * Get the configuration for a push service.
     *
     * @param  string  $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getConfig($name)
    {
        $name = $name ?: $this->getDefaultService();

        // To get the push service configuration, we will just pull each of the
        // service configurations and get the configurations for the given name.
        // If the configuration doesn't exist, we'll throw an exception and bail.
        $connections = $this->app['config']['push::services'];

        if (is_null($config = array_get($connections, $name)))
        {
            throw new \InvalidArgumentException("Push service [$name] not configured.");
        }

        return $config;
    }
}