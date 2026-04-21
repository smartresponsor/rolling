<?php

declare(strict_types=1);

namespace Tests\Role\Policy;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Infrastructure\Cache\InMemoryCache;
use App\Rolling\Policy\Decorator\V2\CachedPdpV2;
use App\Rolling\Policy\Obligation\Obligation;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\Service\Cache\SubjectEpochs;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use PHPUnit\Framework\TestCase;

final class CachedPdpV2Test extends TestCase
{
    /**
     * @return void
     */
    public function testHitMissAndBypass(): void
    {
        $calls = 0;
        $inner = new class($calls) implements PdpV2Interface {
            /**
             * @param int $calls
             */
            public function __construct(private int &$calls)
            {
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

                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };
        $cache = new InMemoryCache();
        $epochs = new SubjectEpochs();
        $pdp = new CachedPdpV2($inner, $cache, $epochs, 60);

        $sid = new SubjectId('u1');
        $act = new PermissionKey('a');
        $sc = Scope::global();
        $ctx = ['x' => 1];

        $d1 = $pdp->check($sid, $act, $sc, $ctx);
        $d2 = $pdp->check($sid, $act, $sc, $ctx);
        $this->assertSame($d1->isAllow(), $d2->isAllow());
        $this->assertSame(1, $calls, 'second call should hit cache');

        // bypass when obligations not empty
        $inner2 = new class implements PdpV2Interface {
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
                return DecisionWithObligations::allow('with-obl', Obligations::empty()->with(new Obligation('mask', ['f' => 'g'])));
            }
        };
        $pdp2 = new CachedPdpV2($inner2, $cache, $epochs, 60);
        $d3 = $pdp2->check($sid, $act, $sc, $ctx);
        $this->assertTrue($d3->isAllow());
        // immediately call again; still 0 cache hit because bypass
        $d4 = $pdp2->check($sid, $act, $sc, $ctx);
        $this->assertTrue($d4->isAllow());
    }

    /**
     * @return void
     */
    public function testInvalidationBump(): void
    {
        $calls = 0;
        $inner = new class($calls) implements PdpV2Interface {
            /**
             * @param int $calls
             */
            public function __construct(private int &$calls)
            {
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

                return DecisionWithObligations::deny('fresh', Obligations::empty());
            }
        };
        $cache = new InMemoryCache();
        $epochs = new SubjectEpochs();
        $pdp = new CachedPdpV2($inner, $cache, $epochs, 60);

        $sid = new SubjectId('u2');
        $act = new PermissionKey('read');
        $sc = Scope::tenant('t1');
        $ctx = ['q' => ['a' => 2]];

        $pdp->check($sid, $act, $sc, $ctx);
        $pdp->check($sid, $act, $sc, $ctx);
        $this->assertSame(1, $calls, 'hit cache');

        // bump should invalidate (epoch changes)
        $epochs->bump('u2');
        $pdp->check($sid, $act, $sc, $ctx);
        $this->assertSame(2, $calls, 'after bump -> miss');
    }
}
