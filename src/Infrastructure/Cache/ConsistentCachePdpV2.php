<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Service\Consistency\TokenSet;
use Closure;
use App\Policy\V2\DecisionWithObligations;
use App\ServiceInterface\Policy\PdpV2Interface;

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
        private readonly Closure        $tokenFn, // fn(?string $subjectId): TokenSet
    ) {}

    /**
     * @param \App\Entity\Role\SubjectId $s
     * @param \App\Entity\Role\PermissionKey $a
     * @param \App\Entity\Role\Scope $sc
     * @param array $context
     * @return \Policy\Role\V2\DecisionWithObligations
     */
    public function check(\App\Entity\Role\SubjectId $s, \App\Entity\Role\PermissionKey $a, \App\Entity\Role\Scope $sc, array $context = []): DecisionWithObligations
    {
        /** @var TokenSet $tok */
        $sid = $s->value();
        $act = $a->value();
        $tok = ($this->tokenFn)($sid);

        $key = $this->makeKey($sid, $act, $sc->key(), $context, (string) $tok);
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
