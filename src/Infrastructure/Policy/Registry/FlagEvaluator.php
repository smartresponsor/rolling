<?php

declare(strict_types=1);

namespace App\Infrastructure\Policy\Registry;

use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

final class FlagEvaluator
{
    /**
     * @param array<string,mixed> $flag
     * @param array<string,mixed> $ctx
     */
    public function isEnabled(array $flag, SubjectId $subject, PermissionKey $action, Scope $scope, array $ctx): bool
    {
        if (!isset($flag['when']) || !is_array($flag['when']) || $flag['when'] === []) {
            return true;
        }
        foreach ($flag['when'] as $cond) {
            if (is_array($cond) && $this->matchCond($cond, $subject, $action, $ctx)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array<string,mixed> $c
     * @param array<string,mixed> $ctx
     */
    private function matchCond(array $c, SubjectId $subject, PermissionKey $action, array $ctx): bool
    {
        if (isset($c['tenantId']) && ($ctx['tenantId'] ?? null) !== $c['tenantId']) {
            return false;
        }
        if (isset($c['env']) && ($ctx['env'] ?? null) !== $c['env']) {
            return false;
        }
        if (isset($c['action']) && $c['action'] !== $action->value()) {
            return false;
        }
        if (isset($c['percent'])) {
            $by = (string) ($c['by'] ?? 'subjectId');
            $id = $by === 'tenantId' ? (string) ($ctx['tenantId'] ?? '') : $subject->value();
            if ($id === '') {
                return false;
            }
            $pct = (int) $c['percent'];
            if ($this->bucket($id) >= $pct) {
                return false;
            }
        }
        return true;
    }

    private function bucket(string $s): int
    {
        return hexdec(substr(hash('sha256', $s), 0, 8)) % 100;
    }
}
