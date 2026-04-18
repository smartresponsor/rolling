<?php

declare(strict_types=1);

namespace App\Policy\Decorator\V2;

use App\Infrastructure\Audit\AuditRecord;
use App\Infrastructure\Audit\ObligationSummary;
use App\InfrastructureInterface\Audit\AuditWriterInterface;
use App\Policy\V2\DecisionWithObligations;
use App\ServiceInterface\Policy\PdpV2Interface;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;
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
     * @param \App\ServiceInterface\Policy\PdpV2Interface $inner
     * @param \App\InfrastructureInterface\Audit\AuditWriterInterface $writer
     */
    public function __construct(private readonly PdpV2Interface $inner, private readonly AuditWriterInterface $writer)
    {
    }

    /**
     * @param \App\Entity\Role\SubjectId $subject
     * @param \App\Entity\Role\PermissionKey $action
     * @param \App\Entity\Role\Scope $objectScope
     * @param array $context
     * @return \App\Policy\V2\DecisionWithObligations
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
                reason: $d->reason(),
                obligations: ObligationSummary::summarize($d->obligations()),
                context: $context,
            );
            $this->writer->write($rec);
        } catch (Throwable $e) {
            error_log('AuditingPdp::check audit fallback: ' . $e->getMessage());
        }
        return $d;
    }
}
