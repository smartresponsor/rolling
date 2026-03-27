<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Role\Rebac;

use src\ServiceInterface\Role\Rebac\NamespaceConstraintInterface;

/**
 * Config-driven constraints. Allowed transitions modeled as set of pairs.
 */
final class NamespaceConstraint implements NamespaceConstraintInterface
{
    /** @var array */
    private array $allow = [];
    private bool $enforceTenantBoundary;

    /**
     * @param array $allowedPairs
     * @param bool $enforceTenantBoundary
     */
    public function __construct(array $allowedPairs = [['subject', 'group'], ['group', 'permission'], ['permission', 'resource']], bool $enforceTenantBoundary = true)
    {
        foreach ($allowedPairs as $p) {
            $a = $p[0] ?? '';
            $b = $p[1] ?? '';
            if ($a !== '' && $b !== '') {
                $this->allow[$a][$b] = true;
            }
        }
        $this->enforceTenantBoundary = $enforceTenantBoundary;
    }

    /**
     * @param string $fromNamespace
     * @param string $toNamespace
     * @return bool
     */
    public function canTraverse(string $fromNamespace, string $toNamespace): bool
    {
        return isset($this->allow[$fromNamespace][$toNamespace]);
    }

    /**
     * @param string $fromTenant
     * @param string $toTenant
     * @return bool
     */
    public function isTenantAllowed(string $fromTenant, string $toTenant): bool
    {
        return !$this->enforceTenantBoundary || $fromTenant === $toTenant;
    }
}
