<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Admin\Guard;

use App\Service\Admin\Dto\ApprovalRequest;

interface ApprovalGuardInterface
{
    public function isSatisfied(ApprovalRequest $req): bool;

    public function remaining(ApprovalRequest $req): int;
}
