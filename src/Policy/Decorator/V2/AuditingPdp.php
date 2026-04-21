<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Decorator\V2;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Infrastructure\Audit\AuditRecord;
use App\Rolling\Infrastructure\Audit\ObligationSummary;
use App\Rolling\InfrastructureInterface\Audit\AuditWriterInterface;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;

final class AuditingPdp implements PdpV2Interface
{
    /**
     * @param PdpV2Interface       $inner
     * @param AuditWriterInterface $writer
     */
    public function __construct(private readonly PdpV2Interface $inner, private readonly AuditWriterInterface $writer)
    {
    }

    /**
     * @param SubjectId     $subject
     * @param PermissionKey $action
     * @param Scope         $objectScope
     * @param array         $context
     *
     * @return DecisionWithObligations
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
        } catch (\Throwable $e) {
            error_log('AuditingPdp::check audit fallback: '.$e->getMessage());
        }

        return $d;
    }
}
