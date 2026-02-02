<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Tools\Policy;

/**
 * Lightweight PEL spec loader (JSON first, YAML via ext/yaml if available,
 * fallback to very naive YAML subset).
 * @return array<string,mixed>
 */
function load_pel(string $path): array
{
    $raw = (string)file_get_contents($path);
    $raw_trim = ltrim($raw);
    if ($raw_trim !== '' && $raw_trim[0] === '{') {
        $d = json_decode($raw, true);
        if (is_array($d)) return $d;
    }
    if (function_exists('yaml_parse')) {
        $d = yaml_parse($raw);
        if (is_array($d)) return $d;
    }
    // naive YAML (subset)
    $obj = ['version' => 1, 'rules' => []];
    foreach (preg_split('/\r?\n/', $raw) as $ln) {
        $ln = trim($ln);
        if ($ln === '' || str_starts_with($ln, '#')) continue;
        if (preg_match('/^- \{(.+)\}$/', $ln, $m)) {
            $pairs = array_map('trim', explode(',', $m[1]));
            $row = [];
            foreach ($pairs as $p) {
                if (!str_contains($p, ':')) continue;
                [$k, $v] = array_map('trim', explode(':', $p, 2));
                $row[$k] = trim($v, " '\"");
            }
            $obj['rules'][] = $row;
        }
    }
    return $obj;
}

/** @return array<string,array<string,mixed>> keyed by rule id */
function index_rules(array $spec): array
{
    $idx = [];
    $rules = $spec['rules'] ?? [];
    if (!is_array($rules)) return $idx;
    foreach ($rules as $r) {
        $id = (string)($r['id'] ?? '');
        if ($id === '') continue;
        $idx[$id] = $r;
    }
    return $idx;
}

/** Validate a single 'when' expression against PEL v1 supported grammar. */
function is_valid_when_expr(string $expr): bool
{
    $expr = trim($expr);
    if ($expr === '') return false;
    if (preg_match('/^subject\.roles\s+contains\s+[A-Za-z0-9_\-]+$/', $expr)) return true;
    if (preg_match('/^action\s*==\s*[A-Za-z0-9_\-]+$/', $expr)) return true;
    if (preg_match('/^subject\.id\s*==\s*resource\.ownerId$/', $expr)) return true;
    if (preg_match('/^resource\.type\s+in\s+\[([A-Za-z0-9_,\-\s]+)\]$/', $expr)) return true;
    return false;
}
