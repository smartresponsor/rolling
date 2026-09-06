<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Admin\Guard;

use App\Rolling\DTO\Admin\AdminApprovalRequestDTO;

interface ApprovalGuardInterface
{
    public function isSatisfied(AdminApprovalRequestDTO $req): bool;

    public function remaining(AdminApprovalRequestDTO $req): int;
}
