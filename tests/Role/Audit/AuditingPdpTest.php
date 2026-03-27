<?php

declare(strict_types=1);

namespace Tests\Role\Audit;

use App\Audit\Role\{AuditRecord, AuditWriter};
use PHPUnit\Framework\TestCase;
use Policy\Role\Decorator\V2\AuditingPdp;
use Policy\Role\Obligation\Obligations;
use Policy\Role\V2\DecisionWithObligations;
use PolicyInterface\Role\PdpV2Interface;
use src\Entity\Role\Scope;
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class AuditingPdpTest extends TestCase
{
    public function testAuditingDecoratorWritesRecord(): void
    {
        $inner = new class implements PdpV2Interface {
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $c = []): DecisionWithObligations
            {
                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };
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
        $this->assertNotNull($writer->last);
        $this->assertSame('u1', $writer->last->subjectId);
        $this->assertSame('ALLOW', $writer->last->decision);
    }
}
