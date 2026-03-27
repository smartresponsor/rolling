<?php

declare(strict_types=1);

namespace App\Audit\Role\Export;

use Throwable;

/**
 *
 */

/**
 *
 */
final class RetryingExporter implements ExporterInterface
{
    /**
     * @param \App\Audit\Role\Export\ExporterInterface $inner
     * @param int $retries
     * @param int $baseMs
     */
    public function __construct(private readonly ExporterInterface $inner, private readonly int $retries = 2, private readonly int $baseMs = 50) {}

    /**
     * @throws \Throwable
     */
    /**
     * @param iterable $records
     * @param string $path
     * @return void
     * @throws \Throwable
     */
    public function export(iterable $records, string $path): void
    {
        $attempt = 0;
        begin:
        $attempt++;
        try {
            $this->inner->export($records, $path);
            return;
        } catch (Throwable $e) {
            if ($attempt <= $this->retries + 1) {
                usleep($this->backoff($attempt) * 1000);
                goto begin;
            }
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    /**
     * @param int $attempt
     * @return int
     * @throws \Exception
     */
    private function backoff(int $attempt): int
    {
        $pow = min(2000, $this->baseMs * (1 << ($attempt - 1)));
        $j = random_int(0, (int) ($pow * 0.2));
        return min(2000, $pow + $j);
    }
}
