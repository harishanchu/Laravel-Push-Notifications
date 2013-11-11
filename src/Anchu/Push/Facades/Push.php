<?php namespace Anchu\Push\Facades;

use Illuminate\Support\Facades\Facade;

class Push extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'push'; }

}