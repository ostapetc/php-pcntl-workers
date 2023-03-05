<?php

require __DIR__ . '/vendor/autoload.php';

//https://github.com/vladimir163/lead-generator
//https://docs.google.com/document/d/12ia3kVyMn0WAaPzXOdRNbqKKE6YTx1MYwuELch7AaVw/edit#

use Spatie\Async\Pool;

$filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'log/result.log';
if (file_exists($filePath)) {
    unlink($filePath);
}

$pool = Pool::create();
$pool->concurrency(100);
$pool->timeout(1);

$startTime = time();

for($i = 1; $i <= 10000; $i++) {
    echo "Add job {$i}\n";

    $job = $pool->add(function() use ($i, $filePath) {
        $content = md5(random_bytes(2048));
        $result = "job_{$i} {$content}";

        sleep(2);
        file_put_contents($filePath, $result . PHP_EOL, FILE_APPEND);

        return $result;
    })->then(function ($result) {
        echo "Job result: {$result}" . PHP_EOL;
    })->timeout(function () {
                
    });
}

$pool->wait();

$tookTime = time() - $startTime;

echo "Start time " . date("Y-m-d H:i:s", $startTime) . "\n";
echo "Now " . date("Y-m-d H:i:s") . "\n";
echo "Took time {$tookTime} sec.\n";


