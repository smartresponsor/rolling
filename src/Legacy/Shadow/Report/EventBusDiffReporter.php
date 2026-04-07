<?php
declare(strict_types=1);

namespace App\Legacy\Shadow\Report;

use App\Event\Role\EventBusInterface;

/**
 *
 */

/**
 *
 */
final class EventBusDiffReporter implements DiffReporterInterface
{

    /**
     * @param \App\Event\Role\EventBusInterface $bus
     * @param string $type
     */
    public function __construct(private readonly EventBusInterface $bus, private string $type = 'role.policy.shadow.diff')
    {
    }

    /**
     * @param array $payload
     * @return void
     */
    public function report(array $payload): void
    {
        $this->bus->emit($this->type, $payload);
    }
}
