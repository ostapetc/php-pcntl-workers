<?php

require __DIR__ . '/vendor/autoload.php';

//https://github.com/vladimir163/lead-generator
//https://docs.google.com/document/d/12ia3kVyMn0WAaPzXOdRNbqKKE6YTx1MYwuELch7AaVw/edit#

use Spatie\Async\Pool;

$filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'log/result.log';
if (file_exists($filePath)) {
    unlink($filePath);
}

$logInfo = function (string $msg) use ($filePath) {
    file_put_contents($filePath, $msg . PHP_EOL, FILE_APPEND);
};

$pool = Pool::create();
$pool->concurrency(100);

//Из требований: Если обработка заявок определенной категории невозможна, остальные
$pool->timeout(2.2);

$startTime = time();

$generator = new \LeadGenerator\Generator();

$generator->generateLeads(100, function (\LeadGenerator\Lead $lead) use ($pool, $logInfo)  {
    $pool->add(function() use ($lead, $logInfo) {
        $timout = random_int(2, 3);
        sleep($timout);

        return $lead;
    })->then(function (\LeadGenerator\Lead $lead) use ($logInfo) {
        $msg = sprintf(
            "%s | %s | %s | %s",
            $lead->id,
            $lead->categoryName,
            date('Y-m-d H:i:s'),
            'success'
        );

        $logInfo($msg);
    })->timeout(function () use ($lead, $logInfo) {
        $msg = sprintf(
            "%s | %s | %s | %s",
            $lead->id,
            $lead->categoryName,
            date('Y-m-d H:i:s'),
            'timeout error'
        );

        $logInfo($msg);
    });

    printf("Added job %s | %s | %s\n", $lead->id, $lead->categoryName, date('Y-m-d H:i:s'));
});

$pool->wait();

$tookTime = time() - $startTime;

echo "Start time " . date("Y-m-d H:i:s", $startTime) . "\n";
echo "Now " . date("Y-m-d H:i:s") . "\n";
echo "Took time {$tookTime} sec.\n";


