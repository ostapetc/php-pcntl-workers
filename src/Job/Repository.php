<?php

namespace App\Job;

use LeadGenerator\Generator;
use LeadGenerator\Lead;

class Repository implements RepositoryInterface
{
    private Generator $leadGenerator;

    public function __construct(Generator $leadGenerator)
    {
        $this->leadGenerator = $leadGenerator;
    }

    public function getJobs(int $count): iterable
    {
        $leads = [];

        $this->leadGenerator->generateLeads($count, function (Lead $lead) use (&$leads)  {
            $leads[] = $lead;
        });

        return $leads;
    }
}