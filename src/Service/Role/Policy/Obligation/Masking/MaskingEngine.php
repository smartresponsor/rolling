<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Role\Policy\Obligation\Masking;

use App\InfraInterface\Role\Policy\MaskingRuleRepositoryInterface;
use App\ServiceInterface\Role\Policy\Obligation\ObligationApplierInterface;

/**
 *
 */

/**
 *
 */
final class MaskingEngine implements ObligationApplierInterface
{
    /**
     * @param \App\InfraInterface\Role\Policy\MaskingRuleRepositoryInterface $repo
     */
    public function __construct(private readonly MaskingRuleRepositoryInterface $repo) {}

    /**
     * @param array $subject
     * @param string $action
     * @param array $resource
     * @param array $context
     * @return array
     */
    public function apply(array $subject, string $action, array $resource, array $context = []): array
    {
        $tenant = $context['tenant'] ?? ($resource['tenant'] ?? ($subject['tenant'] ?? null));
        $rtype = (string) ($resource['type'] ?? 'object');
        $roles = $subject['roles'] ?? [];

        $rules = $this->repo->find($rtype, $action, $tenant, $roles);
        if (!$rules) {
            return ['resource' => $resource, 'meta' => ['maskApplied' => false, 'changedKeys' => []]];
        }

        $changed = [];
        foreach ($rules as $r) {
            $mask = $r['mask'] ?? [];
            if (!empty($mask['drop'])) {
                foreach ($mask['drop'] as $k) {
                    if (array_key_exists($k, $resource)) {
                        unset($resource[$k]);
                        $changed[$k] = 'drop';
                    }
                }
            }
            if (!empty($mask['redact'])) {
                foreach ($mask['redact'] as $k) {
                    if (array_key_exists($k, $resource)) {
                        $resource[$k] = self::redact((string) $resource[$k]);
                        $changed[$k] = 'redact';
                    }
                }
            }
            if (!empty($mask['hash'])) {
                foreach ($mask['hash'] as $k) {
                    if (array_key_exists($k, $resource)) {
                        $resource[$k] = hash('sha256', (string) $resource[$k]);
                        $changed[$k] = 'hash';
                    }
                }
            }
        }

        return [
            'resource' => $resource,
            'meta' => [
                'maskApplied' => !empty($changed),
                'changedKeys' => $changed,
                'ruleCount' => count($rules),
            ],
        ];
    }

    /**
     * @param string $s
     * @return string
     */
    private static function redact(string $s): string
    {
        $len = mb_strlen($s);
        if ($len <= 2) {
            return str_repeat('*', $len);
        }
        $keep = max(1, int($len * 0.25));
        return mb_substr($s, 0, $keep) . str_repeat('*', $len - $keep);
    }
}
