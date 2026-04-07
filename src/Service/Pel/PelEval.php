<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Service\Pel;

use Throwable;

final class PelEval
{
    /**
     * @param array<string, mixed> $ctx
     */
    public static function eval(string $expr, array $ctx): bool
    {
        $e = strtr($expr, [
            ' and ' => ' && ',
            ' or ' => ' || ',
        ]);

        $e = preg_replace_callback('/\b(subject\.role|action|resource\.type|attrs\.[A-Za-z0-9_\-]+)\b/', static function (array $m) use ($ctx): string {
            $k = $m[0];
            $v = null;

            if ($k === 'action') {
                $v = $ctx['action'] ?? null;
            } elseif ($k === 'subject.role') {
                $v = $ctx['subject.role'] ?? null;
            } elseif ($k === 'resource.type') {
                $v = $ctx['resource.type'] ?? null;
            } elseif (str_starts_with($k, 'attrs.')) {
                $key = substr($k, 6);
                $v = $ctx['attrs.' . $key] ?? null;
            }

            if (is_string($v)) {
                return "'" . str_replace("'", "\\'", $v) . "'";
            }
            if (is_numeric($v)) {
                return (string) $v;
            }
            if (is_bool($v)) {
                return $v ? 'true' : 'false';
            }

            return 'null';
        }, $e) ?? '';

        $e = preg_replace('/\b([^\s]+)\s+in\s+\[(.*?)\]/', 'in_array($1, [$2], true)', $e) ?? '';

        if (preg_match('/[^A-Za-z0-9_\-\(\)\[\]\,\'\"\s\&\|\!\.]/', $e) === 1) {
            return false;
        }

        try {
            return (bool) (include __DIR__ . '/pel_runtime.php');
        } catch (Throwable) {
            return false;
        }
    }
}
