<?php

namespace Serp;

class CustomSearch
{
    // private $_apiKey = '';
    // private $_cseCx = '';
    private $_baseUrl = '';
    private $_domainList;
    private $_messageSender;
    function __construct(string $apiKey, string $cseCx, \Serp\DomainList $domainList, $messageSender)
    {
        // $this->_apiKey = $apiKey;
        // $this->_cseCx = $cseCx;
        $this->_baseUrl =
            "https://www.googleapis.com/customsearch/v1?key=${apiKey}&cx=${cseCx}&q=%s&start=%u";
        $this->_domainList = $domainList;
        $this->_messageSender = $messageSender;
    }

    public function search(Keyword $keyword)
    {
        $name = $keyword->getName();
        $volume = $keyword->getVolume();
        $volume /= 10;
        for ($p = 0; $p < $volume; $p++) {
            $url = sprintf($this->_baseUrl, urlencode($name), ($p * 10) + 1);
            $json = $this->getJson($url);
            if (!$json[0]) {
                $this->_messageSender->send(['error' => $json[1]]);
                die();
            }
            $jsonObject = json_decode($json[1]);
            $this->jsonHandler($keyword, $jsonObject, $p);
            $msg = ['progress' => ($p + 1) / ceil($volume)];
            $this->_messageSender->send($msg);
        }
        $this->_messageSender->incrementCurrent();
    }

    private function getJson(string $url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = '';
        $json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($json === false) {
            $return = [false, curl_error($ch)];
        } else if ($httpcode == 429) {
            $return = [false, json_decode($json)->error->message];
        } else {
            $return = [true, $json];
        }
        curl_close($ch);
        return $return;
    }

    private function jsonHandler(Keyword $keyword, $jsonObject, int $p)
    {
        if (!isset($jsonObject->items)) {
            $this->_messageSender->send(['error' => 'Something Went Wrong In JSON Object']);
            throw new \Exception('Something Went Wrong In JSON Object', 1);
        }
        foreach ($jsonObject->items as $index => $item) {
            $domain = $this->_domainList->isDomainOfInterest($item->link);
            if (!$domain[0]) {
                continue;
            }
            $index += ($p * 10) + 1;
            $keyword->addDomainRank($domain[1], $index, $item->link);
        }
    }
}
