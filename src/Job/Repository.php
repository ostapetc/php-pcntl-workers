<?php

namespace App\Job;

use LeadGenerator\Generator;
use LeadGenerator\Lead;

class Repository implements RepositoryInterface
{
    private const JOB_COUNT = 10000;

    private Generator $leadGenerator;

    public function __construct(Generator $leadGenerator)
    {
        $this->leadGenerator = $leadGenerator;
    }

    public function getJobs(): iterable
    {
        $leads = [];

        $this->leadGenerator->generateLeads(self::JOB_COUNT, function (Lead $lead) use (&$leads)  {
            $leads[] = $lead;
        });

        return $leads;
    }
}