<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Admin\Guard;

use App\Service\Admin\Dto\ApprovalRequest;
use App\ServiceInterface\Admin\Guard\ApprovalGuardInterface;

/**
 *
 */

/**
 *
 */
final class FourEyesApprovalGuard implements ApprovalGuardInterface
{
    /**
     * @param \Admin\Dto\ApprovalRequest $req
     * @return bool
     */
    public function isSatisfied(ApprovalRequest $req): bool
    {
        return $req->status === ApprovalRequest::STATUS_PENDING
            && count($req->approvers) >= $req->requiredApprovals;
    }

    /**
     * @param \Admin\Dto\ApprovalRequest $req
     * @return int
     */
    public function remaining(ApprovalRequest $req): int
    {
        return max(0, $req->requiredApprovals - count($req->approvers));
    }
}
