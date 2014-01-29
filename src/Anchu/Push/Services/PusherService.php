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
    public function trigger($channel, $data, $options = array())
    {
        if (is_string($channel) === true) {
            $channel = array($channel);
        }

        if (count($channel) > 100) {
            throw new \LengthException('An event can be triggered on a maximum of 100 channels in a single call.');
        }

        $sUrl = $this->config['url'] . '/events';
        $dataEncoded = json_encode($data);

        $postParams = array();
        $postParams['name'] = isset($options['event']) ? $options['event'] : '';
        $postParams['data'] = $dataEncoded;
        $postParams['channels'] = $channel;

        if (isset($options['socketId'])) {
            $postParams['socket_id'] = $options['socketId'];
        }

        $postValue = json_encode($postParams);
        $queryParams['body_md5'] = md5($postValue);

        # Create the signed signature...
        $signed_query = $this->build_auth_query_string(
            $this->config['authKey'],
            $this->config['secret'],
            'POST',
            $sUrl,
            $queryParams
        );

        $fullUrl = $this->settings['server'] . ':' . $this->settings['port'] . $sUrl . '?' . $signed_query;

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
    public function buildAuthQueryString(
        $authKey,
        $authSecret,
        $requestMethod,
        $requestPath,
        $queryParams = array(),
        $authVersion = '1.0',
        $authTimestamp = null
    ) {
        $params = array();
        $params['auth_key'] = $authKey;
        $params['auth_timestamp'] = (is_null($authTimestamp) ? time() : $authTimestamp);
        $params['auth_version'] = $authVersion;

        $params = array_merge($params, $queryParams);
        ksort($params);

        $stringToSign = "$requestMethod\n" . $requestPath . "\n" . $this->arrayImplode('=', '&', $params);

        $authSignature = hash_hmac('sha256', $stringToSign, $authSecret, false);

        $params['auth_signature'] = $authSignature;
        ksort($params);

        $auth_query_string = $this->arrayImplode('=', '&', $params);

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
    public static function arrayImplode($glue, $separator, $array)
    {
        if (!is_array($array)) {
            return $array;
        }
        $string = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $val = implode(',', $val);
            }
            $string[] = "{$key}{$glue}{$val}";

        }
        return implode($separator, $string);
    }

    /**
     * Fetch channel information for a specific channel.
     *
     * @param string $channel The name of the channel
     * @param array $params Additional parameters for the query e.g. $params = array( 'info' => 'connection_count' )
     * @return object
     */
    public function getChannelInfo($channel, $params = array())
    {
        $response = $this->get('/channels/' . $channel, $params);

        if ($response['status'] == 200) {
            $response = json_decode($response['body']);
        } else {
            $response = false;
        }

        return $response;
    }

    /**
     * Fetch a list containing all channels
     *
     * @param array $params Additional parameters for the query e.g. $params = array( 'info' => 'connection_count' )
     * @return array
     */
    public function getChannels($params = array())
    {
        $response = $this->get('/channels', $params);

        if ($response['status'] == 200) {
            $response = json_decode($response['body']);
            $response->channels = get_object_vars($response->channels);
        } else {
            $response = false;
        }

        return $response;
    }

    /**
     * GET arbitrary REST API resource using a synchronous http client.
     * All request signing is handled automatically.
     *
     * @param string path Path excluding /apps/APP_ID
     * @param params array API params (see http://pusher.com/docs/rest_api)
     *
     * @return See Pusher API docs
     */
    public function get($path, $params = array())
    {
        $sUrl = $this->config['url'] . $path;

        # Create the signed signature...
        $signed_query = $this->buildAuthQueryString(
            $this->config['authKey'],
            $this->config['secret'],
            'GET',
            $sUrl,
            $params
        );

        $fullUrl = $this->config['server'] . ':' . $this->config['port'] . $sUrl . '?' . $signed_query;

        $ch = $this->create_curl($fullUrl, $this->config['timeOut']);

        $response = $this->exec_curl($ch);

        if ($response['status'] == 200) {
            $response['result'] = json_decode($response['body'], true);
        } else {
            $response = false;
        }

        return $response;
    }

    /**
     * Creates a socket signature
     *
     * @param int $socketId
     * @param string $customData
     * @return string
     */
    public function socketAuth($channel, $socketId, $customData = false)
    {
        if ($customData == true) {
            $signature = hash_hmac(
                'sha256',
                $socketId . ':' . $channel . ':' . $customData,
                $this->config['secret'],
                false
            );
        } else {
            $signature = hash_hmac('sha256', $socketId . ':' . $channel, $this->config['secret'], false);
        }

        $signature = array('auth' => $this->config['authKey'] . ':' . $signature);
        // add the custom data if it has been supplied
        if ($customData) {
            $signature['channel_data'] = $customData;
        }
        return json_encode($signature);

    }

    /**
     * Creates a presence signature (an extension of socket signing)
     *
     * @param int $socket_id
     * @param string $user_id
     * @param mixed $user_info
     * @return string
     */
    public function presence_auth($channel, $socketId, $userId, $userInfo = false)
    {

        $userData = array('user_id' => $userId);
        if ($userInfo == true) {
            $userData['user_info'] = $userInfo;
        }

        return $this->socketAuth($channel, $socketId, json_encode($userData));
    }


}
