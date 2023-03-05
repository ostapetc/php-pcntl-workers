<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RunTest extends TestCase
{
    private const JOB_COUNT = 10000;

    private const LEAD_CATEGORIES = [
        'Buy auto',
        'Buy house',
        'Get loan',
        'Cleaning',
        'Learning',
        'Car wash',
        'Repair smth',
        'Barbershop',
        'Pizza',
        'Car insurance',
        'Life insurance',
    ];

    public function testRun(): void
    {
        $startTime = time();

        $lines = $this->runAndGetResultLogLines();

        $tookTime = time() - $startTime;

        //took less than 10 minutes
        self::assertLessThan(60 * 10, $tookTime);

        $leadIds = [];

        foreach ($lines as $line) {
            $line = explode("|", $line);
            self::assertCount(4, $line);

            $leadId = (int) trim($line[0]);
            $leadCategory = trim($line[1]);
            $datetime = trim($line[2]);

            self::assertGreaterThan(0, $leadId);
            self::assertContains($leadCategory, self::LEAD_CATEGORIES);
            self::assertEquals(date('Y-m-d H:i'), date(date('Y-m-d H:i'), strtotime($datetime)));

            $leadIds[] = $leadId;
        }

        //assert all lead ids are present
        for ($id = 1; $id <= self::JOB_COUNT; $id++) {
            self::assertContains($id, $leadIds);
        }
    }

    private function runAndGetResultLogLines(): array
    {
        $logPath = realpath(dirname(__FILE__) . '/../log') . '/result.log';

        if (file_exists($logPath)) {
            unlink($logPath);
        }

        exec("php " . realpath(dirname(__FILE__) . '/../run.php'));

        self::assertFileExists($logPath);

        return file($logPath);
    }
}

