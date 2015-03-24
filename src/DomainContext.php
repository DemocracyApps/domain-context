<?php namespace DemocracyApps\DomainContext;

/*
* This file is part of the DemocracyApps\domain-context package.
*
* Copyright 2015 DemocracyApps, Inc.
*
* See the LICENSE.txt file distributed with this source code for full copyright and license information.
*
*/

class DomainContext {

    protected $currentDomain = null;

    protected $mappedDomainStorage = 'none';

    protected $mappedDomains = null;

    public function __construct ()
    {
        $request = app()->make('Illuminate\Http\Request');
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
            $domains = \DB::table(config('mapped_domain_table_name'))->select('domain_name', 'identifier');
            $this->mappedDomains = array();
            foreach ($domains as $domain) {
                $this->mappedDomains[$domain->name] = $domain->identifier;
            }
        }
    }

    public function getDomain()
    {
        return $this->currentDomain;
    }

    public function getDomainIdentifier() {
        return $this->mappedDomains[$this->currentDomain];
    }

    public function isMapped() {
        if (array_key_exists($this->currentDomain, $this->mappedDomains)) {
            return true;
        }
        return false;
    }
}