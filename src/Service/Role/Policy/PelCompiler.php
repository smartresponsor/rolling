<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Role\Policy;

use InvalidArgumentException;
use src\ServiceInterface\Role\Policy\CompilerInterface;

/**
 * PEL Compiler v1.
 *
 * Input (JSON or YAML if ext/yaml is available):
 * {
 *   "version": 1,
 *   "rules": [
 *     {"id":"allow.admin","when":["subject.roles contains admin"],"effect":"allow","reason":"admin role"},
 *     {"id":"allow.reader","when":["action == read"],"effect":"allow","reason":"read any"},
 *     {"id":"allow.owner.delete","when":["action == delete","subject.id == resource.ownerId"],"effect":"allow","reason":"owner can delete own"},
 *     {"id":"deny.default","effect":"deny","reason":"default deny"}
 *   ]
 * }
 */
final class PelCompiler implements CompilerInterface
{
    /**
     * @param string $name
     * @param string $inputPath
     * @param string|null $outDir
     * @return string
     */
    public function compile(string $name, string $inputPath, ?string $outDir = null): string
    {
        $spec = $this->loadSpec($inputPath);
        if (!isset($spec['rules']) || !is_array($spec['rules'])) {
            throw new InvalidArgumentException('Invalid PEL spec: missing rules');
        }
        $code = $this->generateEvaluator($spec['rules']);
        $outDir = $outDir ?? __DIR__ . '/../../../../var/policy_compiled';
        if (!is_dir($outDir)) {
            @mkdir($outDir, 0775, true);
        }
        $outPath = rtrim($outDir, '/\\') . '/' . $name . '.php';
        file_put_contents($outPath, $code);
        return $outPath;
    }

    /** @return array<string,mixed> */
    private function loadSpec(string $path): array
    {
        $raw = (string) file_get_contents($path);
        $isJson = str_starts_with(ltrim($raw), '{');
        if ($isJson) {
            $d = json_decode($raw, true);
            if (is_array($d)) {
                return $d;
            }
        }
        if (function_exists('yaml_parse')) {
            $d = yaml_parse($raw);
            if (is_array($d)) {
                return $d;
            }
        }
        // naive YAML subset: key: value + simple lists
        $lines = preg_split('/\r?\n/', $raw);
        $curList = null;
        $rules = [];
        $obj = ['version' => 1, 'rules' => []];
        $current = null;
        foreach ($lines as $ln) {
            $ln = trim($ln);
            if ($ln === '' || str_starts_with($ln, '#')) {
                continue;
            }
            if (preg_match('/^rules:\s*$/', $ln)) {
                $curList = 'rules';
                continue;
            }
            if (preg_match('/^- \{(.+)\}$/', $ln, $m)) {
                // - {id: x, effect: allow}
                $pairs = array_map('trim', explode(',', $m[1]));
                $row = [];
                foreach ($pairs as $p) {
                    [$k, $v] = array_map('trim', explode(':', $p, 2));
                    $row[$k] = trim($v, " '\"");
                }
                $obj['rules'][] = $row;
            }
        }
        return $obj;
    }

    /**
     * @param array $rules
     * @return string
     */
    private function generateEvaluator(array $rules): string
    {
        $ifs = [];
        foreach ($rules as $r) {
            $id = (string) ($r['id'] ?? '');
            $effect = (string) ($r['effect'] ?? 'deny');
            $reason = addslashes((string) ($r['reason'] ?? ''));
            $when = $r['when'] ?? [];
            $condPhp = $this->compileWhen($when);
            $allow = $effect === 'allow' ? 'true' : 'false';
            $ifs[] = "if ($condPhp) { return ['allowed'=>$allow,'ruleId':'$id','reason':'$reason']; }";
        }
        $body = implode("\n        ", $ifs);
        return <<<PHP
<?php
return function(array \$subject, string \$action, array \$resource, array \$context): array {
    \$roles = (array)(\$subject['roles'] ?? []);
    \$uid = (string)(\$subject['id'] ?? '');
    \$rtype = (string)(\$resource['type'] ?? '');
    \$owner = (string)(\$resource['ownerId'] ?? '');
    $body
    return ['allowed'=>false,'ruleId'=>'deny.default','reason'=>'default deny'];
};
PHP;
    }

    /**
     * @param array $when
     * @return string
     */
    private function compileWhen(array $when): string
    {
        if (empty($when)) {
            return 'true';
        }
        $parts = [];
        foreach ($when as $expr) {
            $expr = trim((string) $expr);
            if ($expr === '') {
                continue;
            }
            $parts[] = $this->compileExpr($expr);
        }
        return implode(' && ', $parts);
    }

    /**
     * @param string $expr
     * @return string
     */
    private function compileExpr(string $expr): string
    {
        // Supported forms:
        // "subject.roles contains admin"
        // "action == read"
        // "subject.id == resource.ownerId"
        // "resource.type in [doc,project]"
        if (preg_match('/^subject\.roles\s+contains\s+([A-Za-z0-9_\-]+)$/', $expr, $m)) {
            $val = addslashes($m[1]);
            return "in_array('$val', \$roles, true)";
        }
        if (preg_match('/^action\s*==\s*([A-Za-z0-9_\-]+)$/', $expr, $m)) {
            $val = addslashes($m[1]);
            return "\$action === '$val'";
        }
        if (preg_match('/^subject\.id\s*==\s*resource\.ownerId$/', $expr)) {
            return "\$uid !== '' && \$uid === \$owner";
        }
        if (preg_match('/^resource\.type\s+in\s+\[([A-Za-z0-9_,\-\s]+)\]$/', $expr, $m)) {
            $items = array_map('trim', explode(',', $m[1]));
            $arr = implode(',', array_map(fn($s) => "'" . addslashes($s) . "'", $items));
            return "in_array(\$rtype, [$arr], true)";
        }
        // default false to be safe
        return 'false';
    }
}
