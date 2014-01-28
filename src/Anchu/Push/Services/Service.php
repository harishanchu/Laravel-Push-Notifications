<?php namespace Anchu\Push\Services;

class Service {

    /**
     * Utility function used to create the curl object with common settings
     *
     * @param $url
     * @param $timeout
     * @param string $request_method
     * @param array $query_params
     * @return resource
     * @throws \Exception
     */
    public function create_curl($url, $timeout, $request_method = 'GET', $query_params = array())
    {
        $ch = curl_init();
        if ( $ch === false )
        {
            throw new \Exception('Could not initialise cURL!');
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ( "Content-Type: application/json" ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        return $ch;
    }

}