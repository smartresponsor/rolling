<?php

declare(strict_types=1);

namespace App\Service\Shadow\Diff;

use App\Policy\V2\DecisionWithObligations;

final class DecisionDiff
{
    /** @return array<string,mixed> */
    public static function diff(DecisionWithObligations $live, DecisionWithObligations $shadow): array
    {
        $liveObligations = self::obligationSet($live);
        $shadowObligations = self::obligationSet($shadow);

        $onlyLive = array_values(array_diff($liveObligations, $shadowObligations));
        $onlyShadow = array_values(array_diff($shadowObligations, $liveObligations));

        return [
            'equal' => $live->isAllow() === $shadow->isAllow()
                && $live->reason() === $shadow->reason()
                && $onlyLive === []
                && $onlyShadow === [],
            'allow_live' => $live->isAllow(),
            'allow_shadow' => $shadow->isAllow(),
            'reason_live' => $live->reason(),
            'reason_shadow' => $shadow->reason(),
            'obligations_only_live' => $onlyLive,
            'obligations_only_shadow' => $onlyShadow,
        ];
    }

    /** @return list<string> */
    private static function obligationSet(DecisionWithObligations $decision): array
    {
        $encoded = [];

        foreach ($decision->obligations()->toArray() as $obligation) {
            $encoded[] = (string) json_encode($obligation, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        }

        sort($encoded);

        return $encoded;
    }
}
