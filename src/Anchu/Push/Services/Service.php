<?php namespace Anchu\Push\Services;

class Service {

    /**
     * Utility function used to create the curl object with common settings
     *
     * @param $url
     * @param $timeout
     * @return resource
     * @throws \Exception
     */
    public function create_curl($url, $timeout)
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

    /**
     * Utility function to execute curl and create capture response information.
     *
     * @param $ch
     * @return array
     */
    public function exec_curl($ch)
    {
        $response = array();

        $response[ 'body' ] = curl_exec( $ch );
        $response[ 'status' ] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close( $ch );

        return $response;
    }

}