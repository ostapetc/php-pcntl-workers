<?php

namespace App\Log;

use LeadGenerator\Lead;

interface WriterInterface
{
    public function clear(): void;

    public function write(Lead $lead, string $status): void;
}