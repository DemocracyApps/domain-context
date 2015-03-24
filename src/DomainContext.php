<?php namespace DemocracyApps\DomainContext;

/*
* This file is part of the DemocracyApps\domain-context package.
*
* Copyright 2015 DemocracyApps, Inc.
*
* See the LICENSE.txt file distributed with this source code for full copyright and license information.
*
*/
use Illuminate\Http\Request;


class DomainContext {

    private $initialized = false;

    protected $currentDomain = null;

    protected $mappedDomainStorage = 'none';

    protected $mappedDomains = null;

    public function init (Request $request)
    {
        $this->currentDomain = $request->getHttpHost();
        if (config('domain-context.mapped_domain_storage') != null) {
            $this->mappedDomainStorage = config('domain-context.mapped_domain_storage');
        }
        if (config('domain-context.mapped_domains_table') != null) {
            $this->table_name = config('domain-context.mapped_domains_table');
        }
        $domains = null;
        if ($this->mappedDomainStorage == 'config') {
            $this->mappedDomains = config('domain-context.mapped_domains');
        }
        else if ($this->mappedDomainStorage == 'database') {
            $table = config('domain-context.mapped_domain_table_name');
            $domains = \DB::table($table)->select($table.'.domain_name', $table.'.identifier')->get();
            $this->mappedDomains = array();
            foreach ($domains as $domain) {
                $this->mappedDomains[$domain->domain_name] = $domain->identifier;
            }
        }
        $this->initialized = true;
    }

    public function getDomain()
    {
        if (! $this->initialized) {
            $this->init(app()->make('Illuminate\Http\Request'));
        }
        return $this->currentDomain;
    }

    public function getDomainIdentifier() {
        if (! $this->initialized) {
            $this->init(app()->make('Illuminate\Http\Request'));
        }
        return $this->mappedDomains[$this->currentDomain];
    }

    public function isMapped() {
        if (! $this->initialized) {
            $this->init(app()->make('Illuminate\Http\Request'));
        }
        if (array_key_exists($this->currentDomain, $this->mappedDomains)) {
            return true;
        }
        return false;
    }

}