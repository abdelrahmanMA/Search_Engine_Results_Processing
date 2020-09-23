<?php
function detached_exec($cmd)
{
    $pid = pcntl_fork();
    switch ($pid) {
            // fork errror
        case -1:
            return false;

            // this code runs in child process
        case 0:
            // obtain a new process group
            posix_setsid();
            // exec the command
            exec($cmd);
            break;

            // return the child pid in father
        default:
            return $pid;
    }
}
$mode = php_sapi_name();

if ('cli' !== $mode || count($argv) <= 3) {
    die();
}

$apiKey = $argv[1];
$cseCx = $argv[2];
$uniqName = $argv[3];

detached_exec("php src/Serp.php $apiKey $cseCx $uniqName > /dev/null 2>/dev/null &");
