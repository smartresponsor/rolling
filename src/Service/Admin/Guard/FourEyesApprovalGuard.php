<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Admin\Guard;

use App\Rolling\DTO\Admin\AdminApprovalRequestDTO;
use App\Rolling\ServiceInterface\Admin\Guard\ApprovalGuardInterface;

final class FourEyesApprovalGuard implements ApprovalGuardInterface
{
    public function isSatisfied(AdminApprovalRequestDTO $req): bool
    {
        return AdminApprovalRequestDTO::STATUS_PENDING === $req->status
            && count($req->approvers) >= $req->requiredApprovals;
    }

    public function remaining(AdminApprovalRequestDTO $req): int
    {
        return max(0, $req->requiredApprovals - count($req->approvers));
    }
}
