<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Admin;

use App\Rolling\Service\Admin\Dto\ApprovalRequest;

interface ApprovalWorkflowInterface
{
    public function create(string $requestedBy, string $subjectId, string $role, ?string $tenant = null): ApprovalRequest;

    public function approve(string $requestId, string $approverId): ApprovalRequest;

    public function reject(string $requestId, string $approverId, string $reason): ApprovalRequest;

    public function get(string $requestId): ?ApprovalRequest;
}
