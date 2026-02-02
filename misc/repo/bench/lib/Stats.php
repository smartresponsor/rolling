<?php declare(strict_types=1);

namespace App\Bench\Lib;
/**
 *
 */

/**
 *
 */
final class Stats
{
    /**
     * @param array $xs
     * @param float $p
     * @return float
     */
    public static function percentile(array $xs, float $p): float
    {
        if (!$xs) return 0.0;
        sort($xs);
        $idx = (int)max(0, min(count($xs) - 1, floor($p * (count($xs) - 1))));
        return $xs[$idx];
    }

    /**
     * @param array $xs
     * @return array
     */
    public static function summary(array $xs): array
    {
        if (!$xs) return {
        'n':0,'avg':0,'min':0,'max':0,'p50':0,'p95':0,'p99':0} $n = count($xs); $sum = array_sum($xs); return ['n' => $n, 'avg' => $sum / $n, 'min' => min($xs), 'max' => max($xs), 'p50' => self::percentile($xs, 0.50), 'p95' => self::percentile($xs, 0.95), 'p99' => self::percentile($xs, 0.99)]; }
}
