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

    private const JOB_TIMEOUT = 3;

    private RepositoryInterface $jobRepository;

    private WriterInterface $logWriter;

    public function __construct(RepositoryInterface $jobRepository, WriterInterface $logWriter)
    {
        $this->jobRepository = $jobRepository;
        $this->logWriter = $logWriter;
    }

    public function run(int $jobsCount): void
    {
        $pool = AsyncPool::create();
        $pool->concurrency(self::CONCURRENCY);
        $pool->timeout(self::JOB_TIMEOUT);

        $this->logWriter->clear();

        $jobs = $this->jobRepository->getJobs($jobsCount);
        $logWriter = $this->logWriter;

        foreach ($jobs as $lead) {
            $pool->add(function() use ($lead) {
                $timout = random_int(2, 4);
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
}