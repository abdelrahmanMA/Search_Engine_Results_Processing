<?php

namespace Serp;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class KeywordWriter
{
    private $_keywords;
    private $_spreadSheet;
    private $_domains;
    function __construct(DomainList $domains, array $keywords, Spreadsheet $spreadSheet)
    {
        $this->_spreadSheet = $spreadSheet->getActiveSheet();
        $this->_keywords = $keywords;
        $this->_domains = $domains->getDomainList();
        $this->_spreadSheet->fromArray(
            array_merge(['Keyword', 'Volume'], array_keys($this->_domains)),
            NULL,
            'A1'
        );
    }
    public function write_keywords()
    {
        $keywords = $this->_keywords;
        $row = 2;
        $rowIncrement = 0;
        foreach ($keywords as $keyword) {
            foreach ($keyword->getDomainRank() as $domain => $ranks) {
                $column = $this->_domains[$domain];
                foreach ($ranks as $i => $rank) {
                    $rowIncrement = max($i, $rowIncrement);
                    $drank = $rank[0];
                    $url = $rank[1];
                    $this->_spreadSheet->setCellValueByColumnAndRow($column, $row + $i, $drank);
                    $this->_spreadSheet->getCellByColumnAndRow($column, $row + $i)->getHyperlink()->setUrl($url);
                    $this->_spreadSheet->setCellValueByColumnAndRow(1, $row + $i, $keyword->getName());
                    $this->_spreadSheet->setCellValueByColumnAndRow(2, $row + $i, $keyword->getVolume());
                }
            }
            $row += $rowIncrement + 1;
            $rowIncrement = 0;
        }
        for ($c = 1; $c < count($this->_domains) + 3; $c++) {
            $this->_spreadSheet->getColumnDimensionByColumn($c)->setAutoSize(true);
        }
    }
}
