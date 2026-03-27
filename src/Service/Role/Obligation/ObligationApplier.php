<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Service\Role\Obligation;

use App\Domain\Role\Port\ObligationStorePort;

/**
 *
 */

/**
 *
 */
final class ObligationApplier
{
    /**
     * @param \App\Domain\Role\Port\ObligationStorePort $store
     */
    public function __construct(private readonly ObligationStorePort $store) {}

    /**
     * @param string $tenant
     * @param string $relation
     * @param array $decision expects ['allowed'=>bool]
     * @param array $attrs subject/resource attributes
     * @param array|null $resource viewable resource (optional)
     * @param string $version
     * @return array{view:array<string,mixed>|null, headers:array<int,array<string,string>>, actions:array<int,array<string,mixed>>}
     */
    public function apply(string $tenant, string $relation, array $decision, array $attrs, ?array $resource = null, string $version = 'active'): array
    {
        $cfg = $this->store->load($tenant, $version);
        $rules = (array) ($cfg['rules'] ?? []);
        $effect = ($decision['allowed'] ?? false) ? 'ALLOW' : 'DENY';
        $view = $resource;
        $headers = [];
        $applied = [];

        foreach ($rules as $rule) {
            $matchRel = (string) ($rule['match']['relation'] ?? '');
            $matchEff = (string) ($rule['match']['effect'] ?? 'ANY');
            if ($matchRel !== '' && $matchRel !== $relation) {
                continue;
            }
            if ($matchEff !== 'ANY' && $matchEff !== $effect) {
                continue;
            }
            if (!$this->whenOk((array) ($rule['when'] ?? []), $attrs)) {
                continue;
            }
            foreach ((array) ($rule['actions'] ?? []) as $act) {
                $t = (string) ($act['type'] ?? '');
                if ($t === 'mask' && $view !== null) {
                    $path = (string) ($act['path'] ?? '');
                    $with = (string) ($act['with'] ?? '***');
                    $this->maskPath($view, $path, $with);
                    $applied[] = ['type' => 'mask', 'path' => $path];
                } elseif ($t === 'redact' && $view !== null) {
                    $path = (string) ($act['path'] ?? '');
                    $this->redactPath($view, $path);
                    $applied[] = ['type' => 'redact', 'path' => $path];
                } elseif ($t === 'header') {
                    $headers[] = ['name' => (string) $act['name'], 'value' => (string) $act['value']];
                    $applied[] = ['type' => 'header', 'name' => $act['name'] ?? ''];
                } elseif ($t === 'purpose') {
                    $headers[] = ['name' => 'X-Data-Purpose', 'value' => (string) ($act['tag'] ?? 'unspecified')];
                    $applied[] = ['type' => 'purpose', 'tag' => $act['tag'] ?? ''];
                }
            }
        }
        return ['view' => $view, 'headers' => $headers, 'actions' => $applied];
    }

    /**
     * @param array $conds
     * @param array $attrs
     * @return bool
     */
    private function whenOk(array $conds, array $attrs): bool
    {
        foreach ($conds as $c) {
            if (isset($c['equals'])) {
                $path = (string) ($c['equals']['path'] ?? '');
                $val = $c['equals']['value'] ?? null;
                $got = $this->getByPath($attrs, $path);
                if ($got !== $val) {
                    return false;
                }
            }
            if (isset($c['exists'])) {
                $path = (string) ($c['exists']['path'] ?? '');
                $got = $this->getByPath($attrs, $path);
                if ($got === null) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param array|null $obj
     * @param string $path
     * @param string $with
     */
    private function maskPath(?array &$obj, string $path, string $with): void
    {
        if ($obj === null || $path === '') {
            return;
        }
        $ref = &$this->refByPath($obj, $path);
        if ($ref === null) {
            return;
        }
        if (is_string($ref)) {
            if (str_contains($with, '#')) {
                $digits = preg_replace('/\D+/', '', $ref);
                $keep = substr($digits, -substr_count($with, '#')) ?: '';
                $ref = preg_replace('/\d+/', $keep, $ref);
            } else {
                $ref = $with;
            }
        } else {
            $ref = $with;
        }
    }

    /**
     * @param array|null $obj
     * @param string $path
     */
    private function redactPath(?array &$obj, string $path): void
    {
        if ($obj === null || $path === '') {
            return;
        }
        $parts = explode('.', $path);
        $last = array_pop($parts);
        $cur = &$obj;
        foreach ($parts as $p) {
            if (!is_array($cur) || !array_key_exists($p, $cur)) {
                return;
            }
            $cur = &$cur[$p];
        }
        if (is_array($cur) && array_key_exists($last, $cur)) {
            unset($cur[$last]);
        }
    }

    /**
     * @param array $obj
     * @param string $path
     * @return mixed
     */
    private function getByPath(array $obj, string $path): mixed
    {
        if ($path === '') {
            return null;
        }
        $cur = $obj;
        foreach (explode('.', $path) as $p) {
            if (!is_array($cur) || !array_key_exists($p, $cur)) {
                return null;
            }
            $cur = $cur[$p];
        }
        return $cur;
    }

    /** @return mixed by-ref */
    private function &refByPath(array &$obj, string $path)
    {
        $null = null;
        if ($path === '') {
            return $null;
        }
        $cur = &$obj;
        foreach (explode('.', $path) as $p) {
            if (!is_array($cur) || !array_key_exists($p, $cur)) {
                return $null;
            }
            $cur = &$cur[$p];
        }
        return $cur;
    }
}
