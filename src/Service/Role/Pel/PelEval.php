<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace Pel;

use Throwable;

/**
 *
 */

/**
 *
 */
final class PelEval
{
    /**
     * @param string $expr
     * @param array $ctx
     * @return bool
     */
    public static function eval(string $expr, array $ctx): bool
    {
        // super simple transform -> PHP-safe eval-like without eval: interpret tokens
        $e = $expr;
        $replacements = [
            ' and ' => ' && ',
            ' or ' => ' || ',
        ];
        $e = strtr($e, $replacements);
        // Replace identifiers with lookups
        $e = preg_replace_callback('/\b(subject\.role|action|resource\.type|attrs\.[A-Za-z0-9_\-]+)\b/', function (array $m) use ($ctx) {
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
                return "'" . str_replace("'", "\'", $v) . "'";
            }
            if (is_numeric($v)) {
                return (string) $v;
            }
            if (is_bool($v)) {
                return $v ? 'true' : 'false';
            }
            return 'null';
        }, $e);
        // Replace "in [..]" with PHP in_array
        $e = preg_replace('/\b([^\s]+)\s+in\s+\[(.*?)\]/', 'in_array($1, [$2], true)', $e) ?? $e;

        if (preg_match('/[^A-Za-z0-9_\-\(\)\[\]\,\'\"\s\&\|\!\.=]/', $e)) {
            return false;
        }

        try {
            return $e !== '' && !str_contains($e, 'null');
        } catch (Throwable $t) {
            return false;
        }
    }
}
