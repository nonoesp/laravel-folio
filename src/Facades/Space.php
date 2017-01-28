<?php
namespace Nonoesp\Space\Facades;
use Illuminate\Support\Facades\Facade;
class Space extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'space'; }

}
