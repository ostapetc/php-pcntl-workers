<?php

namespace App\Job;

interface RepositoryInterface
{
    public function getJobs(int $count): iterable;
}