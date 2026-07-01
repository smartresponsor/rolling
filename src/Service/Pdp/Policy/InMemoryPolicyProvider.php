<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Pdp\Policy;

use App\Rolling\ServiceInterface\Pdp\PolicyDecisionProviderInterface;

class InMemoryPolicyProvider implements PolicyDecisionProviderInterface
{
    private array $rules = [];

    public function addRule(string $id, array $rule): void
    {
        $this->rules[$id] = $rule;
    }

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
