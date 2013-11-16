<?php namespace Anchu\Push;

class PushManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

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
    public function __construct($app)
    {
        $this->app = $app;
    }
}