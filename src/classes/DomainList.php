<?php

namespace Serp;

use \Pdp;

class DomainList
{
    private $_domainList = array();
    private $_rules;

    function __construct(string $domains)
    {
        $manager = new Pdp\Manager(new Pdp\Cache(), new Pdp\CurlHttpClient());
        $this->_rules = $manager->getRules(Pdp\Manager::PSL_URL);

        $domains = preg_split('/\r\n|[\r\n]/', $domains);
        foreach ($domains as $index => $domain) {
            if (empty($domain)) {
                continue;
            }
            $this->_domainList[$this->uniformDomain($domain)] = $index + 3;
        }
    }

    public function isDomainOfInterest(string $domain): array
    {
        $domain = $this->uniformDomain($domain);
        return [array_key_exists($domain, $this->_domainList), $domain];
    }

    public function getDomainList(): array
    {
        return $this->_domainList;
    }

    private function addScheme(string $url): string
    {
        $scheme = 'https://';
        return parse_url($url, PHP_URL_SCHEME) === null ?
            $scheme . ltrim($url, '/') : $url;
    }

    private function uniformDomain(string $domain): string
    {

        $domain = $this->_rules->getICANNDomain(parse_url($this->addScheme($domain), PHP_URL_HOST));
        $domain = $this->_rules->getICANNDomain($domain);
        $domain = $domain->getRegistrableDomain();
        return $domain;
    }
}
