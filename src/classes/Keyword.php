<?php

namespace Serp;

class Keyword
{
    private $_name;
    private $_volume;
    private $_domainRank = array();

    function __construct(string $name, int $volume)
    {
        $this->_name = $name;
        $this->_volume = $volume;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function getVolume(): int
    {
        return $this->_volume;
    }

    public function getDomainRank(): array
    {
        return $this->_domainRank;
    }

    public function setVolume(int $volume)
    {
        $this->_volume = $volume;
    }

    public function addDomainRank(string $domain, int $rank, string $url)
    {
        if (!array_key_exists($domain, $this->_domainRank)) {
            $this->_domainRank[$domain] = array();
        }
        array_push($this->_domainRank[$domain], [$rank, $url]);
    }
}
