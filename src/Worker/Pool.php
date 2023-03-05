<?php

namespace App\Worker;

use App\Job\RepositoryInterface;
use App\Log\Writer;
use App\Log\WriterInterface;
use Spatie\Async\Pool as AsyncPool;
use LeadGenerator\Lead;

final class Pool
{
    private const CONCURRENCY = 100;

    private const JOB_TIMEOUT = 2.5;

    private RepositoryInterface $jobRepository;

    private WriterInterface $logWriter;

    public function __construct(RepositoryInterface $jobRepository, WriterInterface $logWriter)
    {
        $this->jobRepository = $jobRepository;
        $this->logWriter = $logWriter;
    }

    public function run(): void
    {
        $pool = AsyncPool::create();
        $pool->concurrency(self::CONCURRENCY);
        $pool->timeout(self::JOB_TIMEOUT);

        $this->logWriter->clear();

        $jobs = $this->jobRepository->getJobs();
        $logWriter = $this->logWriter;

        foreach ($jobs as $lead) {
            $pool->add(function() use ($lead) {
                $timout = random_int(2, 3);
                sleep($timout);

                return $lead;
            })->then(function (Lead $lead) use ($logWriter) {
                $logWriter->write($lead, 'success');
            })->timeout(function () use ($lead, $logWriter) {
                $logWriter->write($lead, 'timeout');
            })->catch(function (\Exception $exception) use ($lead, $logWriter) {
                $logWriter->write($lead, 'error: '. get_class($exception) . ' ' . $exception->getMessage());
            });

            printf("Added job %s | %s | %s\n", $lead->id, $lead->categoryName, date('Y-m-d H:i:s'));
        }

        $pool->wait();
    }

    private function getLogWriter(): callable
    {
        $filePath = realpath(dirname(__FILE__) . '/../../') . '/log/result.log';
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return function (string $msg) use ($filePath) {
            file_put_contents($filePath, $msg . PHP_EOL, FILE_APPEND);
        };
    }
}