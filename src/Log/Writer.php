<?php

namespace App\Log;

use LeadGenerator\Lead;

class Writer implements WriterInterface
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = realpath(dirname(__FILE__) . '/../../') . '/log/result.log';
    }

    public function clear(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    public function write(Lead $lead, string $status): void
    {
        $msg = sprintf(
            "%s | %s | %s | %s",
            $lead->id,
            $lead->categoryName,
            date('Y-m-d H:i:s'),
            $status
        );

        file_put_contents($this->filePath, $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}