<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Admin\Guard;

use App\Rolling\Service\Admin\Dto\AdminApprovalRequestDto;
use App\Rolling\ServiceInterface\Admin\Guard\ApprovalGuardInterface;

final class FourEyesApprovalGuard implements ApprovalGuardInterface
{
    public function isSatisfied(AdminApprovalRequestDto $req): bool
    {
        return AdminApprovalRequestDto::STATUS_PENDING === $req->status
            && count($req->approvers) >= $req->requiredApprovals;
    }

    public function remaining(AdminApprovalRequestDto $req): int
    {
        return max(0, $req->requiredApprovals - count($req->approvers));
    }
}
