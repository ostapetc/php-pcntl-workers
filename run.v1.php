<?php

require __DIR__ . '/vendor/autoload.php';

//https://github.com/vladimir163/lead-generator
//https://docs.google.com/document/d/12ia3kVyMn0WAaPzXOdRNbqKKE6YTx1MYwuELch7AaVw/edit#

use Spatie\Async\Pool;

// The maximum amount of processes which can run simultaneously.
//const concurrency = 10;

// The maximum amount of time a process may take to finish in seconds
// (decimal places are supported for more granular timeouts).
const timeout = 2;

// Configure how long the loop should sleep before re-checking the process statuses in microseconds.
const re_checking_process_status_interval  = 50000;

const LEADS_COUNT = 10000;

$workerProcessedLeadsPerMinute = ceil(60 / timeout);
$workersCount = ceil(LEADS_COUNT / $workerProcessedLeadsPerMinute);
$workersCount += 50;



echo "workerProcessedLeadsPerMinute = {$workerProcessedLeadsPerMinute}\n";
echo "concurrent workers min count = {$workersCount}\n";

$workersCount = 300;

//die;

$writeLog = function(string $file, string $msg): void {
    $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'log/' . $file . '.log';
    file_put_contents($path, $msg . "\n", FILE_APPEND);
};

$debugLog = function(string $msg) use ($writeLog): void {
    $writeLog('debug', $msg);
};

$resultLog = function(string $msg) use ($writeLog): void {
    $writeLog('result', $msg);
};

$pool = Pool::create()
    ->concurrency($workersCount)
    ->timeout(timeout)
    ->autoload(__DIR__ . '/vendor/autoload.php')
    ->sleepTime(re_checking_process_status_interval);

$startTime = microtime(true);

$generator = new \LeadGenerator\Generator();
$generator->generateLeads(LEADS_COUNT, function (\LeadGenerator\Lead $lead) use ($pool, $resultLog, $debugLog) {
    $date = date('Y-m-d H:i:s');

    $pool->add(function () use ($debugLog, $lead) {
        $debugLog("Processing lead {$lead->id}");
        sleep(2);
    })->then(function ($output) use ($resultLog, $date, $lead) {
        $msg = sprintf("%s | %s | %s", $lead->id, $lead->categoryName, $date);
        $resultLog($msg);
    })->catch(function (Throwable $exception) use ($debugLog, $lead) {
        $debugLog("Processing lead {$lead->id} error: {$exception->getMessage()}");
    })->timeout(function () use ($debugLog, $lead){
        $debugLog("Processing lead {$lead->id} timeout");
    });

    echo "Added job {$lead->id} {$lead->categoryName}\n";
});

$pool->wait();

echo sprintf("Took time %s sec.\n", microtime(true) - $startTime);

echo "Done\n";

