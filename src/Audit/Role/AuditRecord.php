<?php
declare(strict_types=1);

namespace App\Audit\Role;

/**
 *
 */

/**
 *
 */
final class AuditRecord
{
    /**
     * @param int $ts
     * @param string $subjectId
     * @param string $action
     * @param string $scopeKey
     * @param string $decision
     * @param string $reason
     * @param array $obligations @param array<string,mixed> $context
     * @param array $context
     */
    public function __construct(
        public int    $ts,
        public string $subjectId,
        public string $action,
        public string $scopeKey,
        public string $decision,
        public string $reason = '',
        public array  $obligations = [],
        public array  $context = []
    )
    {
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
