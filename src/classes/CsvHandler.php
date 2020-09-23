<?php

namespace Serp;

class CsvHandler
{
    private $_keywords = array();
    private $_csvDomains = '';
    private $_keywordMaker;

    function __construct(string $file, string $keywordMaker)
    {
        $this->_keywordMaker = $keywordMaker;
        $this->parseCsv($file);
    }

    public function getCsvDomains(): string
    {
        return $this->_csvDomains;
    }

    public function getKeywords(): array
    {
        return $this->_keywords;
    }

    private function parseCsv($input_file)
    {
        if (!isset($input_file) || (($handle = fopen($input_file, 'r')) === FALSE)) {
            return FALSE;
        }
        $row = 0;
        $columns = 0;

        while (($data = fgetcsv($handle)) !== FALSE) {
            $row++;
            if ($row == 1) {
                $columns = count($data);
                if ($columns > 2) {
                    $this->_csvDomains = implode('\n', array_splice($data, 2));
                }
                continue;
            }
            $name = trim($data[0]);
            $volume = (int) $data[1];
            if (!isset($this->_keywords[$name])) {
                $this->_keywords[$name] = new $this->_keywordMaker($name, $volume);
            } else {
                $this->_keywords[$name]->setVolume(
                    max($volume, $this->_keywords[$name]->getVolume())
                );
            }
        }
        fclose($handle);
    }
}
