<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Role\Admin\Guard;

use Admin\Dto\ApprovalRequest;

/**
 *
 */

/**
 *
 */
interface ApprovalGuardInterface
{
    /**
     * @param \Admin\Dto\ApprovalRequest $req
     * @return bool
     */
    public function isSatisfied(ApprovalRequest $req): bool;

    /**
     * @param \Admin\Dto\ApprovalRequest $req
     * @return int
     */
    public function remaining(ApprovalRequest $req): int;
}
