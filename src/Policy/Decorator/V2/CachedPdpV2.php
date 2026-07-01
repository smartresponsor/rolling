<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Decorator\V2;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\InfrastructureInterface\Cache\CacheInterface;
use App\Rolling\Policy\Obligation\Obligation;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\Service\Cache\SubjectCacheEpochRegistry;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;

/**
 * Cache decorator for PDP v2 decisions.
 */
final class CachedPdpV2 implements PdpV2Interface
{
    public function __construct(
        private readonly PdpV2Interface $inner,
        private readonly CacheInterface $cache,
        private readonly SubjectCacheEpochRegistry $epochs,
        private readonly int $ttlSeconds = 600,
    ) {
    }

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

        // Bypass cache when obligations are present.
        if (!empty($dec->obligations()->all())) {
            return $dec;
        }

        // Store serialized data to reduce cross-process storage risk.
        $this->cache->set($key, self::toArray($dec), $this->ttlSeconds);

        return $dec;
    }

    private static function ctxHash(array $ctx): string
    {
        $norm = self::normalize($ctx);

        return hash('sha256', json_encode($norm, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private static function normalize(array $a): array
    {
        ksort($a);
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                /* @var array<string,mixed> $v */
                $a[$k] = self::normalize($v);
            }
        }

        return $a;
    }

    private static function key(string $sid, string $act, string $scope, string $ctxHash, int $epoch): string
    {
        return "v2:$sid:$scope:$act:ctx:$ctxHash:se:$epoch";
        // Можно добавить версию кода/политик, если появится (pv:{policyRev})
    }

    /** @return array{allow:bool,reason:string,obligations:list<array{type:string,params:array<string,mixed>}>} */
    private static function toArray(DecisionWithObligations $d): array
    {
        $obs = [];
        foreach ($d->obligations()->all() as $o) {
            $obs[] = ['type' => $o->type, 'params' => $o->params];
        }

        return ['allow' => $d->isAllow(), 'reason' => $d->reason(), 'obligations' => $obs];
    }

    /** @param array{allow:bool,reason:string,obligations:list<array{type:string,params:array<string,mixed>}>} $a */
    private static function fromArray(array $a): DecisionWithObligations
    {
        $obs = Obligations::empty();
        foreach ($a['obligations'] as $o) {
            $obs = $obs->with(new Obligation((string) $o['type'], (array) ($o['params'] ?? [])));
        }

        return $a['allow'] ? DecisionWithObligations::allow((string) $a['reason'], $obs)
            : DecisionWithObligations::deny((string) $a['reason'], $obs);
    }
}
