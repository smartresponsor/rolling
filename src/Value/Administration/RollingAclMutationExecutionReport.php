<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe report for Administering/host diagnostics around ACL mutation execution.
 */
final readonly class RollingAclMutationExecutionReport
{
    /** @param list<RollingAclMutationExecutionEvent> $events */
    public function __construct(
        private RollingAclMutationExecutionFilter $filter,
        private RollingAclMutationExecutionSummary $summary,
        private array $events = [],
    ) {
    }

    public function filter(): RollingAclMutationExecutionFilter
    {
        return $this->filter;
    }

    public function summary(): RollingAclMutationExecutionSummary
    {
        return $this->summary;
    }

    /** @return list<RollingAclMutationExecutionEvent> */
    public function events(): array
    {
        return $this->events;
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'filter' => $this->filter->toSafeArray(),
            'summary' => $this->summary->toSafeArray(),
            'events' => array_map(
                static fn (RollingAclMutationExecutionEvent $event): array => $event->toSafeArray(),
                $this->events,
            ),
        ];
    }
}
