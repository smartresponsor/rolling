<?php
declare(strict_types=1);

namespace Tests\Role\Consistency;

use App\Infrastructure\Cache\ConsistentCachePdpV2;
use App\Service\Consistency\{Composer};
use App\Service\Consistency\Policy\Token as PolicyToken;
use App\Service\Consistency\Rebac\Token as RebacToken;
use PHPUnit\Framework\TestCase;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\PolicyInterface\PdpV2Interface;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class TokenAndCacheTest extends TestCase
{
    /**
     * @return void
     */
    public function testCompositeTokenAndCacheInvalidation(): void
    {
        $policyRev = 1;
        $rebacRev = 5;
        $composer = new Composer(
            policyTokenFn: function () use (&$policyRev) { return new PolicyToken($policyRev); },
            rebacTokenFn: function () use (&$rebacRev) { return new RebacToken($rebacRev); },
            subjectEpochFn: fn(string $sid) => 0
        );
        $calls = 0;
        $inner = new class($calls) implements PdpV2Interface {
            public int $calls = 0;
            /** @var int */
            private $ref;

            /**
             * @param int $callsRef
             */
            public function __construct(int &$callsRef)
            {
                $this->calls = 0;
                $this->ref =& $callsRef;
            }

            /**
             * @param \App\Entity\Role\SubjectId $s
             * @param \App\Entity\Role\PermissionKey $a
             * @param \App\Entity\Role\Scope $sc
             * @param array $ctx
             * @return \Policy\Role\V2\DecisionWithObligations
             */
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $ctx = []): DecisionWithObligations
            {
                $this->calls++;
                $this->ref++;
                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };
        $pdp = new ConsistentCachePdpV2($inner, fn(?string $sid) => $composer->token($sid));

        $sid = new SubjectId('u1');
        $act = new PermissionKey('message.read');
        $sc = Scope::global();
        $pdp->check($sid, $act, $sc);
        $pdp->check($sid, $act, $sc);
        $this->assertSame(1, $inner->calls, 'cache hit with same token');

        // bump policy rev -> token changes -> cache miss
        $policyRev = 2;
        $pdp->check($sid, $act, $sc);
        $this->assertSame(2, $inner->calls, 'cache invalidated after token change');
    }
}
