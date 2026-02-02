<?php
declare(strict_types=1);

namespace App\Cache\Role;

use App\Consistency\Role\TokenSet;
use App\Entity\Role\App\src\Entity\Role\SubjectId;
use App\Entity\Role\App\src\Entity\Role\Scope;
use App\Entity\Role\App\src\Entity\Role\PermissionKey;
use Closure;
use Policy\Role\V2\DecisionWithObligations;
use PolicyInterface\Role\PdpV2Interface;

/**
 *
 */

/**
 *
 */
final class ConsistentCachePdpV2 implements PdpV2Interface
{
    /** @var array */
    private array $cache = [];

    /**
     * @param \PolicyInterface\Role\PdpV2Interface $inner
     * @param \Closure $tokenFn
     */
    public function __construct(
        private readonly PdpV2Interface $inner,
        private readonly Closure        $tokenFn // fn(?string $subjectId): TokenSet
    )
    {
    }

    /**
     * @param \src\Entity\Role\SubjectId $s
     * @param \src\Entity\Role\PermissionKey $a
     * @param \src\Entity\Role\Scope $sc
     * @param array $context
     * @return \Policy\Role\V2\DecisionWithObligations
     */
    public function check(\src\Entity\Role\SubjectId $s, \src\Entity\Role\PermissionKey $a, \src\Entity\Role\Scope $sc, array $context = []): DecisionWithObligations
    {
        /** @var TokenSet $tok */
        $tok = ($this->tokenFn)((string)$s);

        $key = $this->makeKey((string)$s, (string)$a, $sc->key(), $context, (string)$tok);
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }
        $dec = $this->inner->check($s, $a, $sc, $context);
        $this->cache[$key] = $dec;
        return $dec;
    }

    /**
     * @param string $sid
     * @param string $act
     * @param string $scopeKey
     * @param array $ctx
     * @param string $tokenStr
     * @return string
     */
    private function makeKey(string $sid, string $act, string $scopeKey, array $ctx, string $tokenStr): string
    {
        ksort($ctx);
        return sha1(json_encode([$sid, $act, $scopeKey, $ctx, $tokenStr], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
