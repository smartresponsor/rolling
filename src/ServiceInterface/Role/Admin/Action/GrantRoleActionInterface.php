<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Role\Admin\Action;

use Admin\Dto\ApprovalRequest;

/**
 *
 */

/**
 *
 */
interface GrantRoleActionInterface
{
    /**
     * Apply the role grant described by the request (idempotent).
     */
    public function apply(ApprovalRequest $req): void;
}
