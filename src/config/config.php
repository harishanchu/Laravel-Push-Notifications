<?php
return array(

    /*
	|--------------------------------------------------------------------------
	| Default Push Service Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the Push services below you wish
	| to use as your default service for all real time push messaging.
	|
	*/

    'default' => 'service1',

    /*
    |--------------------------------------------------------------------------
    | Push services
    |--------------------------------------------------------------------------
    |
    | Here are each of the Push service setup for your application.
    |
    */

    'services' => array(

        'pusher' => array(
            'provider'      => 'pusher',
            'authKey'       => '',
            'secret'        => '',
            'appId'         => '',
            'timeOut'       => 30,
        ),

        'faye' => array(
            'provider'      => 'faye',
            'host'          => '',
            'port'          => '',
            'mountPoint'    => '',
            'timeOut'       => 30
        )

    ),
);