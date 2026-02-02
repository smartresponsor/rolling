<?php declare(strict_types=1);

namespace App\Bench\Lib;
/**
 *
 */

/**
 *
 */
final class Timer
{
    /**
     * @return float
     */
    public static function now(): float
    {
        return microtime(true);
    }

    /**
     * @param float $dt
     * @return float
     */
    public static function ms(float $dt): float
    {
        return $dt * 1000.0;
    }
}
