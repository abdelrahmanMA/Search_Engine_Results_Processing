<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
$result_dir =  __DIR__ . '/tmp/result.txt';
if (!file_exists($result_dir)) {
    $error = json_encode(['error' => 'Something Wen\'t wrong, contact administrator']);
    echo "data: {$error}\n\n";
    flush();
    die();
}
$result = file_get_contents($result_dir);
echo "data: {$result}\n\n";
flush();
$data = json_decode($result);
if (array_key_exists('progress', $data)) {
    $progress = $data->progress;
    if ($progress == 100) {
        unlink($result_dir);
    }
}
