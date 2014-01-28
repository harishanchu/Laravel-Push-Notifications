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

    /**
     * Build the required HMAC'd auth string
     *
     * @param $authKey
     * @param $authSecret
     * @param $requestMethod
     * @param $requestPath
     * @param array $queryParams
     * @param string $authVersion
     * @param null $authTimestamp
     * @return string
     */
    public function buildAuthQueryString($authKey, $authSecret, $requestMethod, $requestPath, $queryParams = array(), $authVersion = '1.0', $authTimestamp = null)
    {
        $params = array();
        $params['auth_key'] = $authKey;
        $params['auth_timestamp'] = (is_null($authTimestamp)?time() : $authTimestamp);
        $params['auth_version'] = $authVersion;

        $params = array_merge($params, $queryParams);
        ksort($params);

        $stringToSign = "$requestMethod\n" . $requestPath . "\n" . $this->arrayImplode( '=', '&', $params );

        $authSignature = hash_hmac( 'sha256', $stringToSign, $authSecret, false );

        $params['auth_signature'] = $authSignature;
        ksort($params);

        $auth_query_string = $this->arrayImplode( '=', '&', $params );

        return $auth_query_string;
    }

    /**
     * Implode an array with the key and value pair giving
     * a glue, a separator between pairs and the array
     * to implode.
     *
     * @param string $glue The glue between key and value
     * @param string $separator Separator between pairs
     * @param array $array The array to implode
     * @return string The imploded array
     */
    public static function arrayImplode( $glue, $separator, $array ) {
        if ( ! is_array( $array ) ) return $array;
        $string = array();
        foreach ( $array as $key => $val ) {
            if ( is_array( $val ) )
                $val = implode( ',', $val );
            $string[] = "{$key}{$glue}{$val}";

        }
        return implode( $separator, $string );
    }
    
}
