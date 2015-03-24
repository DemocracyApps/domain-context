<?php namespace DemocracyApps\DomainContext;

use Illuminate\Support\Facades\Facade;

class DomainContextFacade extends Facade
{

    /**
     * The name of the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'DemocracyApps\DomainContext\DomainContext';
    }
}