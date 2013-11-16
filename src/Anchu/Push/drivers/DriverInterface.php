<?php namespace Anchu\Push\Drivers;

interface DriverInterface {

    /**
     * Publish/Trigger message .
     *
     * @param $channel
     * @param $event
     * @param $data
     * @param bool $debug
     * @return mixed
     */
    public function trigger( $channel, $event, $data, $debug = false);

}