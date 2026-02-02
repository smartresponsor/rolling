<?php
declare(strict_types=1);

namespace Tests\Role\Audit;

use App\Audit\Role\{AuditRecord};
use PHPUnit\Framework\TestCase;
use Policy\Role\Decorator\V2\AuditingPdp;
use Policy\Role\Obligation\Obligations;
use Policy\Role\V2\DecisionWithObligations;
use PolicyInterface\Role\PdpV2Interface;
use src\Entity\Role\{Scope};
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
final private class CapturingWriter implements AuditWriter
{
    public ?AuditRecord $last = null;

    /**
     * @param \App\Audit\Role\AuditRecord $rec
     * @return void
     */
    public function write(AuditRecord $rec): void
    {
        $this->last = $rec;
    }
}

public
/**
 * @return void
 */
function testAuditingDecoratorWritesRecord(): void
{
    $inner = new class implements PdpV2Interface {
        /**
         * @param \src\Entity\Role\SubjectId $s
         * @param \src\Entity\Role\PermissionKey $a
         * @param \src\Entity\Role\Scope $sc
         * @param array $c
         * @return \Policy\Role\V2\DecisionWithObligations
         */
        public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $c = []): DecisionWithObligations
        {
            return DecisionWithObligations::allow('ok', Obligations::empty());
        }
    };
    $w = new self::CapturingWriter();
    $pdp = new AuditingPdp($inner, $w);

    $d = $pdp->check(new SubjectId('u1'), new PermissionKey('message.read'), Scope::tenant('t1'), ['ip' => '127.0.0.1']);
    $this->assertTrue($d->isAllow());
    $this->assertNotNull($w->last);
    $this->assertSame('u1', $w->last->subjectId);
    $this->assertSame('ALLOW', $w->last->decision);
}
}
