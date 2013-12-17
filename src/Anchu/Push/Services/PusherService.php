<?php namespace Anchu\Push\Services;

class PusherService extends Service implements ServiceInterface
{
    /**
     * The active pusher service configuration
     */
    protected $config;

    /**
     * Create a new pusher service instance.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->config['server'] = 'http://api.pusherapp.com';
        $this->config['port'] = '80';
        $this->config['url'] = '/apps/' . $this->config['appId'];
    }

    /**
     * Publish/Trigger message .
     *
     * @param $channel
     * @param $data
     * @param $options
     * @return mixed
     */
    public function trigger( $channel, $data, $options = array())
    {
        if( is_string( $channel ) === true ) {
            $channel = array( $channel );
        }

        if( count( $channel ) > 100 ) {
            throw new \LengthException('An event can be triggered on a maximum of 100 channels in a single call.');
        }

        $sUrl = $this->config['url'] . '/events';
        $dataEncoded = json_encode($data);

        $postParams = array();
        $postParams['name'] = isset($options['event'])?$options['event']:'';
        $postParams['data'] = $dataEncoded;
        $postParams['channels'] = $channel;

        if (isset($options['socketId']))
        {
            $postParams['socket_id'] = $options['socketId'];
        }

        $postValue = json_encode($postParams);
        $queryParams['body_md5'] = md5($postValue);
    }
    
}
