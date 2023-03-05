<?php

namespace App\Job;

interface RepositoryInterface
{
    public function getJobs(): iterable;
}