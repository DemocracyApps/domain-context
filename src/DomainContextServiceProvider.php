<?php namespace DemocracyApps\DomainContext;

/*
* This file is part of the DemocracyApps\domain-context package.
*
* Copyright 2015 DemocracyApps, Inc.
*
* See the LICENSE.txt file distributed with this source code for full copyright and license information.
*
*/

use Illuminate\Support\ServiceProvider;

class DomainContextServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('DemocracyApps\DomainContext\DomainContext', function ($app) {
            return new DomainContext();
        });
    }
    
        /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $context = app()->make('DemocracyApps\DomainContext\DomainContext');
        $vv = config('domain-context.view_variable_name');
        if ($vv == null) $vv = 'domainContext';
        view()->share($vv, $context);

        $this->publishes([
            __DIR__ . '/config/domain-context.php' => config_path('domain-context.php')
        ]);

    }
}
