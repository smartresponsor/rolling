<?php

declare(strict_types=1);

namespace Tests\Role\Audit;

<<<<<<< HEAD
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
=======
use App\Audit\Role\{AuditRecord, AuditWriter};
use PHPUnit\Framework\TestCase;
use Policy\Role\Decorator\V2\AuditingPdp;
use Policy\Role\Obligation\Obligations;
use Policy\Role\V2\DecisionWithObligations;
use PolicyInterface\Role\PdpV2Interface;
use src\Entity\Role\Scope;
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa

final class AuditingPdpTest extends TestCase
{
    public function testAuditingDecoratorWritesRecord(): void
    {
        $inner = new class implements PdpV2Interface {
<<<<<<< HEAD
            public function check(SubjectId $subject, PermissionKey $action, Scope $scope, array $context = []): DecisionWithObligations
=======
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $c = []): DecisionWithObligations
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
            {
                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };
<<<<<<< HEAD

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
=======
        $writer = new class implements AuditWriter {
            public ?AuditRecord $last = null;

            public function write(AuditRecord $rec): void
            {
                $this->last = $rec;
            }
        };
        $pdp = new AuditingPdp($inner, $writer);

        $d = $pdp->check(new SubjectId('u1'), new PermissionKey('message.read'), Scope::tenant('t1'), ['ip' => '127.0.0.1']);
        $this->assertTrue($d->isAllow());
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
        $this->assertNotNull($writer->last);
        $this->assertSame('u1', $writer->last->subjectId);
        $this->assertSame('ALLOW', $writer->last->decision);
    }
}
