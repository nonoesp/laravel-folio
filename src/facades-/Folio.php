<?php
namespace Nonoesp\Folio\Facades;
use Illuminate\Support\Facades\Facade;

class Folio extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'folio'; }

}
