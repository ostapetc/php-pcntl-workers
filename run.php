<?php

require __DIR__ . '/vendor/autoload.php';

use App\Job\Repository;
use App\Worker\Pool;
use LeadGenerator\Generator;
use App\Log\Writer as LogWriter;

$jobRepository = new Repository(new Generator());
$logWriter = new LogWriter();

$workerPool = new Pool($jobRepository, $logWriter);
$workerPool->run();

echo "All jobs are processed.\n";

