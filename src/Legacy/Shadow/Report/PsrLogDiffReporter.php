<?php

declare(strict_types=1);

namespace App\Legacy\Shadow\Report;

use Psr\Log\LoggerInterface;

/**
 *
 */

/**
 *
 */
final class PsrLogDiffReporter implements DiffReporterInterface
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $channel
     */
    public function __construct(private readonly LoggerInterface $logger, private string $channel = 'role_shadow') {}

    /**
     * @param array $payload
     * @return void
     */
    public function report(array $payload): void
    {
        $this->logger->info($this->channel, $payload);
    }
}
