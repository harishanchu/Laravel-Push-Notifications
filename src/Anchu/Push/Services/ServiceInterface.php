<?php namespace Anchu\Push\Services;

interface ServiceInterface {

    /**
     * Publish/Trigger message .
     *
     * @param $channel
     * @param $event
     * @param $data
     * @param bool $debug
     * @return mixed
     */
    public function trigger( $channel, $data, $options = array());

}