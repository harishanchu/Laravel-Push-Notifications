<?php namespace Anchu\Push\Services;

class FayeService extends Service implements ServiceInterface
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
    }

    /**
     * Publish/Trigger message .
     *
     * @param $channel
     * @param $event
     * @param $data
     * @param bool $debug
     * @return mixed
     */
    public function trigger($channel, $data, $options = array())
    {
        $sUrl = $this->config['url'];
        $postParams = array();
        $postParams['data'] = $data;
        $postParams['channel'] = '/' . $channel;

        $postValue = json_encode($postParams);

        $fullUrl = $this->config['server'] . ':' . $this->config['port'] . $sUrl;

        $ch = $this->create_curl($fullUrl, $this->config['timeOut']);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postValue);

        $response = $this->exec_curl($ch);

        if ($response['status'] == 200) {
            return true;
        } else {
            return false;
        }
    }

}
