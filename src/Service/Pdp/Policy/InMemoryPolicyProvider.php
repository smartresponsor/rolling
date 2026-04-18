<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Pdp\Policy;

use App\ServiceInterface\Pdp\PolicyDecisionProviderInterface;

/**
 *
 */

/**
 *
 */
class InMemoryPolicyProvider implements PolicyDecisionProviderInterface
{
    /** @var array */
    private array $rules = [];

    /**
     * @param string $id
     * @param array $rule
     * @return void
     */
    public function addRule(string $id, array $rule): void
    {
        $this->rules[$id] = $rule;
    }

    /**
     * @param array $subject
     * @param string $action
     * @param array $resource
     * @param array $context
     * @return bool
     */
    public function isAllowed(array $subject, string $action, array $resource, array $context = []): bool
    {
        $roles = $subject['roles'] ?? [];
        $tenant = $context['tenant'] ?? null;
        foreach ($this->rules as $rule) {
            if (isset($rule['action']) && $rule['action'] !== $action) {
                continue;
            }
            if (isset($rule['resource']) && $rule['resource'] !== ($resource['type'] ?? null)) {
                continue;
            }
            if (isset($rule['tenant']) && $rule['tenant'] !== $tenant) {
                continue;
            }
            if (isset($rule['role']) && !in_array($rule['role'], $roles, true)) {
                continue;
            }
            return true;
        }
        return false;
    }
}
