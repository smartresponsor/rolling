<?php

declare(strict_types=1);

namespace Policy\Role\Decorator\V2;

use App\Audit\Role\{AuditRecord, AuditWriter, ObligationSummary};
use Policy\Role\V2\DecisionWithObligations;
use PolicyInterface\Role\PdpV2Interface;
use src\Entity\Role\{Scope};
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;
use Throwable;

/**
 *
 */

/**
 *
 */
final class AuditingPdp implements PdpV2Interface
{
    /**
     * @param \PolicyInterface\Role\PdpV2Interface $inner
     * @param \App\Audit\Role\AuditWriter $writer
     */
    public function __construct(private readonly PdpV2Interface $inner, private readonly AuditWriter $writer) {}

    /**
     * @param \src\Entity\Role\SubjectId $subject
     * @param \src\Entity\Role\PermissionKey $action
     * @param \src\Entity\Role\Scope $objectScope
     * @param array $context
     * @return \Policy\Role\V2\DecisionWithObligations
     */
    public function check(SubjectId $subject, PermissionKey $action, Scope $objectScope, array $context = []): DecisionWithObligations
    {
        $d = $this->inner->check($subject, $action, $objectScope, $context);
        try {
            $rec = new AuditRecord(
                ts: time(),
                subjectId: $subject->value(),
                action: $action->value(),
                scopeKey: $objectScope->key(),
                decision: $d->isAllow() ? 'ALLOW' : 'DENY',
                reason: $d->reason,
                obligations: ObligationSummary::summarize($d->obligations),
                context: $context,
            );
            $this->writer->write($rec);
        } catch (Throwable $e) {
            // аудит не должен ломать основной поток
        }
        return $d;
    }
}
