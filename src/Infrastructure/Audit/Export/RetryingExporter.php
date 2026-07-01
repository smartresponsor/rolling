<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Audit\Export;

final class RetryingExporter implements \App\Rolling\InfrastructureInterface\Audit\Export\ExporterInterface
{
    public function __construct(private readonly \App\Rolling\InfrastructureInterface\Audit\Export\ExporterInterface $inner, private readonly int $retries = 2, private readonly int $baseMs = 50)
    {
    }

    /**
     * @throws \Throwable
     */
    /**
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
        } catch (\Throwable $e) {
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
     * @throws \Exception
     */
    private function backoff(int $attempt): int
    {
        $pow = min(2000, $this->baseMs * (1 << ($attempt - 1)));
        $j = random_int(0, (int) ($pow * 0.2));

        return min(2000, $pow + $j);
    }
}
