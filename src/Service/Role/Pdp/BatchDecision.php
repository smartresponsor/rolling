<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Role\Pdp;

use App\Service\Role\Pdp\Dto\DecisionResponse;
use src\ServiceInterface\Role\Pdp\BatchDecisionInterface;

/**
 * Simple batch PDP implementation (deterministic, no external deps).
 * Rule sketch:
 *  - admin role → allow all
 *  - reader can read any resource
 *  - writer can write docs/projects; delete only own
 *  - default deny
 */
final class BatchDecision implements BatchDecisionInterface
{
    /** @inheritDoc */
    public function decideMany(array $requests): array
    {
        $out = [];
        foreach ($requests as $req) {
            $start = microtime(true);
            $roles = (array)($req->subject['roles'] ?? []);
            $uid = (string)($req->subject['id'] ?? '');
            $rtype = (string)($req->resource['type'] ?? '');
            $rid = (string)($req->resource['id'] ?? '');
            $owner = (string)($req->resource['ownerId'] ?? '');
            $action = $req->action;

            $allowed = false;
            $rule = 'deny.default';
            $reason = '';

            if (in_array('admin', $roles, true)) {
                $allowed = true;
                $rule = 'allow.admin';
                $reason = 'admin role';
            } elseif ($action === 'read') {
                $allowed = true;
                $rule = 'allow.reader';
                $reason = 'read-any';
            } elseif ($action === 'write' && in_array($rtype, ['doc', 'project'], true) && in_array('writer', $roles, true)) {
                $allowed = true;
                $rule = 'allow.writer';
                $reason = 'writer on doc/project';
            } elseif ($action === 'delete' && $uid !== '' && $uid === $owner) {
                $allowed = true;
                $rule = 'allow.owner';
                $reason = 'owner can delete own';
            }

            $latency = (microtime(true) - $start) * 1000.0;
            $out[] = new DecisionResponse($allowed, $rule, $reason, $latency);
        }
        return $out;
    }
}
