<?php

declare(strict_types=1);

namespace Tests\Role\Audit;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Infrastructure\Audit\AuditRecord;
use App\Rolling\InfrastructureInterface\Audit\AuditWriterInterface as AuditWriter;
use App\Rolling\Policy\Decorator\V2\AuditingPdp;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use PHPUnit\Framework\TestCase;

final class AuditingPdpTest extends TestCase
{
    public function testAuditingDecoratorWritesRecord(): void
    {
        $inner = new class implements PdpV2Interface {
            public function check(SubjectId $subject, PermissionKey $action, Scope $scope, array $context = []): DecisionWithObligations
            {
                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };

        $writer = new class implements AuditWriter {
            public ?AuditRecord $last = null;

            public function write(AuditRecord $record): void
            {
                $this->last = $record;
            }
        };

        $pdp = new AuditingPdp($inner, $writer);

        $decision = $pdp->check(
            new SubjectId('u1'),
            new PermissionKey('message.read'),
            Scope::tenant('t1'),
            ['ip' => '127.0.0.1']
        );

        $this->assertTrue($decision->isAllow());
        $this->assertNotNull($writer->last);
        $this->assertSame('u1', $writer->last->subjectId);
        $this->assertSame('ALLOW', $writer->last->decision);
    }
}
