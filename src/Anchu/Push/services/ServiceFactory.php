<?php namespace Anchu\Push\Services;

class ServiceFactory {

    /**
     * Initiate a service class based on the configuration.
     *
     * @param  array   $config
     * @return
     */
    public function make(array $config)
    {
        if ( ! isset($config['provider']))
        {
            throw new \InvalidArgumentException("A provider must be specified.");
        }

        switch ($config['provider'])
        {
            case 'faye':
                return new FayeService;

            case 'pusher':
                return new PusherService;

            case 'socket-io':
                return new SocketIoService;

            case 'slanger':
                return new SlangerService;
        }

        throw new \InvalidArgumentException("Unsupported provider [{$config['provider']}]");
    }

}