<?php
declare(strict_types=1);

namespace App\Legacy\Metrics\Admin;

/**
 *
 */

/**
 *
 */
final class AdminMetrics
{
    /** @var array */
    private static array $counters = [
        'role_admin_policy_import_total' => 0,
        'role_admin_policy_activate_total' => 0,
        'role_admin_errors_total' => 0,
        'role_admin_rebac_dump_total' => 0,
        'role_admin_rebac_stats_total' => 0,
    ];

    /**
     * @param string $name
     * @param int $delta
     * @return void
     */
    public static function inc(string $name, int $delta = 1): void
    {
        if (!isset(self::$counters[$name])) self::$counters[$name] = 0;
        self::$counters[$name] += $delta;
    }

    /**
     * @return string
     */
    public static function renderPrometheus(): string
    {
        $lines = [];
        foreach (self::$counters as $k => $v) {
            $lines[] = "# TYPE $k counter";
            $lines[] = $k . ' ' . $v;
        }
        return implode("\n", $lines) . "\n";
    }

    /** @return array<string,int> */
    public static function snapshot(): array
    {
        return self::$counters;
    }
}
