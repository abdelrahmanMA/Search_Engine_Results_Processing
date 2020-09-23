<?php

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../vendor/autoload.php';
define('TMPDIR', __DIR__ . '/../tmp/');


$source = php_sapi_name();

if ('cli' !== $source || count($argv) <= 3) {
    die();
}

$apiKey = $argv[1];
$cseCx = $argv[2];
$uniqName = $argv[3];

$keywordsCsv = TMPDIR . "keywords_$uniqName.csv";
$csvHandler = new \Serp\CsvHandler($keywordsCsv, \Serp\Keyword::class);
$keywords = $csvHandler->getKeywords();

$domains = file_get_contents(TMPDIR . "domains_$uniqName.txt");
$domains .= PHP_EOL . $csvHandler->getCsvDomains();
$domainList = new \Serp\DomainList($domains);

$sender = new \Serp\MessageSender(count($keywords));
$customSearch = new \Serp\CustomSearch($apiKey, $cseCx, $domainList, $sender);

foreach ($keywords as $keyword) {
    $customSearch->search($keyword);
}

$spreadsheet = new Spreadsheet();

$keywordWriter = new \Serp\KeywordWriter($domainList, $keywords, $spreadsheet);
$keywordWriter->write_keywords();

$xlsxWriter = new Xlsx($spreadsheet);
$xlsxPath = TMPDIR . "SERP_$uniqName.xlsx";
$xlsxWriter->save($xlsxPath);

unlink(TMPDIR . "domains_$uniqName.txt");
unlink($keywordsCsv);

$sender->send(array('progress' => 0, 'link' => "tmp/SERP_$uniqName.xlsx"));
