<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Audit;

final class AuditRecord
{
    /**
     * @param array $obligations @param array<string,mixed> $context
     */
    public function __construct(
        public int $ts,
        public string $subjectId,
        public string $action,
        public string $scopeKey,
        public string $decision,
        public string $reason = '',
        public array $obligations = [],
        public array $context = [],
    ) {
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'ts' => $this->ts,
            'subjectId' => $this->subjectId,
            'action' => $this->action,
            'scopeKey' => $this->scopeKey,
            'decision' => $this->decision,
            'reason' => $this->reason,
            'obligations' => $this->obligations,
            'context' => $this->context,
        ];
    }
}
