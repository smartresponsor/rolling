<?php
declare(strict_types=1);

namespace Tests\Role\Audit;

use App\Infrastructure\Audit\AuditRecord;
use App\InfrastructureInterface\Audit\AuditWriterInterface as AuditWriter;
use PHPUnit\Framework\TestCase;
use App\Legacy\Policy\Decorator\V2\AuditingPdp;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\PolicyInterface\PdpV2Interface;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

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
