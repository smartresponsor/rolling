<?php

declare(strict_types=1);

namespace App\Policy\Decorator\V2;

use App\Infrastructure\Cache\KeyValueCache;
use App\Legacy\Invalidation\SubjectEpochs;
use App\Policy\Obligation\Obligation;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\PolicyInterface\PdpV2Interface;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;

/**
 * Реальный кеширующий декоратор PDP v2.
 */
final class CachedPdpV2 implements PdpV2Interface
{
    /**
     * @param \App\PolicyInterface\PdpV2Interface $inner
     * @param \App\Infrastructure\Cache\KeyValueCache $cache
     * @param \App\Legacy\Invalidation\SubjectEpochs $epochs
     * @param int $ttlSeconds
     */
    public function __construct(
        private readonly PdpV2Interface $inner,
        private readonly KeyValueCache  $cache,
        private readonly SubjectEpochs  $epochs,
        private readonly int            $ttlSeconds = 600
    )
    {
    }

    /**
     * @param \App\Entity\Role\SubjectId $s
     * @param \App\Entity\Role\PermissionKey $a
     * @param \App\Entity\Role\Scope $sc
     * @param array $context
     * @return \App\Policy\V2\DecisionWithObligations
     */
    public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $context = []): DecisionWithObligations
    {
        $sid = $s->value();
        $act = $a->value();
        $scope = $sc->key();
        $ctxHash = self::ctxHash($context);
        $epoch = $this->epochs->epochFor($sid);
        $key = self::key($sid, $act, $scope, $ctxHash, $epoch);

        $cached = $this->cache->get($key);
        if ($cached instanceof DecisionWithObligations) {
            return $cached;
        }
        if (is_array($cached)) {
            return self::fromArray($cached);
        }

        $dec = $this->inner->check($s, $a, $sc, $context);

        // bypass при obligations != []
        if (!empty($dec->obligations->all())) {
            return $dec;
        }

        // Сохраним сериализованно (меньше рисков на кросс-проц. сторе)
        $this->cache->set($key, self::toArray($dec), $this->ttlSeconds);
        return $dec;
    }

    /**
     * @param array $ctx
     * @return string
     */
    private static function ctxHash(array $ctx): string
    {
        $norm = self::normalize($ctx);
        return hash('sha256', json_encode($norm, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param array $a
     * @return array
     */
    private static function normalize(array $a): array
    {
        ksort($a);
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                /** @var array<string,mixed> $v */
                $a[$k] = self::normalize($v);
            }
        }
        return $a;
    }

    /**
     * @param string $sid
     * @param string $act
     * @param string $scope
     * @param string $ctxHash
     * @param int $epoch
     * @return string
     */
    private static function key(string $sid, string $act, string $scope, string $ctxHash, int $epoch): string
    {
        return "v2:$sid:$scope:$act:ctx:$ctxHash:se:$epoch";
        // Можно добавить версию кода/политик, если появится (pv:{policyRev})
    }

    /** @return array{allow:bool,reason:string,obligations:list<array{type:string,params:array<string,mixed>}>} */
    private static function toArray(DecisionWithObligations $d): array
    {
        $obs = [];
        foreach ($d->obligations->all() as $o) {
            $obs[] = ['type' => $o->type, 'params' => $o->params];
        }
        return ['allow' => $d->isAllow(), 'reason' => $d->reason, 'obligations' => $obs];
    }

    /** @param array{allow:bool,reason:string,obligations:list<array{type:string,params:array<string,mixed>}>} $a */
    private static function fromArray(array $a): DecisionWithObligations
    {
        $obs = Obligations::empty();
        foreach ($a['obligations'] as $o) {
            $obs = $obs->with(new Obligation((string)$o['type'], (array)($o['params'] ?? [])));
        }
        return $a['allow'] ? DecisionWithObligations::allow((string)$a['reason'], $obs)
            : DecisionWithObligations::deny((string)$a['reason'], $obs);
    }
}
