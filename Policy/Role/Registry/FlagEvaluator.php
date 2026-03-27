<?php

declare(strict_types=1);

namespace Policy\Role\Registry;

use src\Entity\Role\{Scope};
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class FlagEvaluator
{
    /**
     * @param array $flag
     * @param \src\Entity\Role\SubjectId $subject
     * @param \src\Entity\Role\PermissionKey $action
     * @param \src\Entity\Role\Scope $scope
     * @param array $ctx
     * @return bool
     */
    public function isEnabled(array $flag, SubjectId $subject, PermissionKey $action, Scope $scope, array $ctx): bool
    {
        if (!isset($flag['when']) || !is_array($flag['when']) || $flag['when'] === []) {
            return true;
        }
        foreach ($flag['when'] as $cond) {
            if ($this->matchCond($cond, $subject, $action, $ctx)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $c @param array<string,mixed> $ctx
     * @param \src\Entity\Role\SubjectId $subject
     * @param \src\Entity\Role\PermissionKey $action
     * @param array $ctx
     * @return bool
     */
    private function matchCond(array $c, SubjectId $subject, PermissionKey $action, array $ctx): bool
    {
        // tenant/env matchers
        if (isset($c['tenantId']) && ($ctx['tenantId'] ?? null) !== $c['tenantId']) {
            return false;
        }
        if (isset($c['env']) && ($ctx['env'] ?? null) !== $c['env']) {
            return false;
        }
        if (isset($c['action']) && $c['action'] !== $action->value()) {
            return false;
        }
        // percentage rollout
        if (isset($c['percent'])) {
            $by = (string) ($c['by'] ?? 'subjectId');
            $id = $by === 'tenantId' ? (string) ($ctx['tenantId'] ?? '') : $subject->value();
            if ($id === '') {
                return false;
            }
            $pct = (int) $c['percent'];
            $bucket = $this->bucket($id);
            if ($bucket >= $pct) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $s
     * @return int
     */
    private function bucket(string $s): int
    {
        $h = substr(hash('sha256', $s), 0, 8);
        $n = hexdec($h);
        return $n % 100;
    }
}
