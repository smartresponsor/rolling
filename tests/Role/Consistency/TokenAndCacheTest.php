<?php

declare(strict_types=1);

namespace Tests\Role\Consistency;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Infrastructure\Cache\ConsistentCachePdpV2;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\Service\Consistency\Composer;
use App\Rolling\Service\Consistency\Policy\Token as PolicyToken;
use App\Rolling\Service\Consistency\Rebac\Token as RebacToken;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use PHPUnit\Framework\TestCase;

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
            subjectEpochFn: fn (string $sid) => 0
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
                $this->ref = &$callsRef;
            }

            /**
             * @param SubjectId     $s
             * @param PermissionKey $a
             * @param Scope         $sc
             * @param array         $ctx
             *
             * @return \Policy\Role\V2\DecisionWithObligations
             */
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $ctx = []): DecisionWithObligations
            {
                ++$this->calls;
                ++$this->ref;

                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };
        $pdp = new ConsistentCachePdpV2($inner, fn (?string $sid) => $composer->token($sid));

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
