<?php
declare(strict_types=1);

namespace App\Shadow\Role\Diff;

use Policy\Role\V2\DecisionWithObligations;

/**
 *
 */

/**
 *
 */
final class DecisionDiff
{
    /** @return array<string,mixed> */
    public static function diff(DecisionWithObligations $live, DecisionWithObligations $shadow): array
    {
        $liveObs = self::obsToSet($live);
        $shadowObs = self::obsToSet($shadow);
        $onlyLive = array_values(array_diff($liveObs, $shadowObs));
        $onlyShadow = array_values(array_diff($shadowObs, $liveObs));
        $allowEq = $live->isAllow() === $shadow->isAllow();
        $reasonEq = $live->reason() === $shadow->reason();
        $obsEq = empty($onlyLive) && empty($onlyShadow);
        return ['equal' => $allowEq && $reasonEq && $obsEq, 'allow_live' => $live->isAllow(), 'allow_shadow' => $shadow->isAllow(), 'reason_live' => $live->reason(), 'reason_shadow' => $shadow->reason(), 'obligations_only_live' => $onlyLive, 'obligations_only_shadow' => $onlyShadow];
    }

    /** @return list<string> */
    private static function obsToSet(DecisionWithObligations $d): array
    {
        $arr = $d->obligations()->toArray();
        $enc = [];
        foreach ($arr as $o) {
            $enc[] = json_encode($o, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        }
        sort($enc);
        return $enc;
    }
}
